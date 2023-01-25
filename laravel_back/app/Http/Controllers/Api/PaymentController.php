<?php

namespace App\Http\Controllers\Api;

use App\Classes\UpdateTotalCount;
use App\Course;
use App\Date;
use App\Event;
use App\Invoice;
use App\Jobs\GenerateCompanyInvoiceFile;
use App\Jobs\SendCmnEmail;
use App\Promocode;
use App\Ticket;
use App\Topic;
use App\Traits\StatsCounter;
use App\User;
use App\UserCourse;
use App\UserTopic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Stripe\Checkout\Session;
use Stripe\Coupon;
use Stripe\Stripe;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    use StatsCounter;

    protected $model = Invoice::class;

    public function __construct() {}

    /* Начало платежа stripe - создание сессии. */
    public function stripeCreateSession (Request $request)
    {
        $payAsCompany = $request->get('payAsCompany');
        $basket = $request->get('basket');
        $user = User::findOrFail($basket['userId']);
        $items = $basket['items'];
        $cost = $basket['cost'];
        $currency = $basket['currency'];
        $promoCode = $basket['promoCode'];
        if (is_array($items)) {
            /* Проверяются на валидность элементы корзины */
            $checkResult = $this->checkItems($items, $user, $promoCode, $currency);

            if ($checkResult['confirmed'] === true) {
                /* Получение отформатированных под stripe элементов корзины */
                $stripeFormatted= $this->getStripeFormattedItems($items, $currency['name']);
                $stripeFormattedItems = $stripeFormatted['items'];
                /* Установка секретного ключа stripe(STRIPE_TEST_SECRET и STRIPE_LIVE_SECRET) */
                Stripe::setApiKey(config('stripe')[config('stripe.mode')]['secret']);
                if ($promoCode) {
                    /* Если к корзине применялся промокод, создается объект купона stripe. */
                    // substr(,0,27) Stripe has name limit of 40 chars
                    /* https://stripe.com/docs/api/coupons/create выбрать PHP в селекторе языков */
                    $couponBody = [
                        'name' => substr($checkResult['promocodeSignature'],0,27),
                        'amount_off' => (int) ($checkResult['promocodeDiscountTotal'] * 100),
                        'duration' => 'once',
                        'currency' => $currency['name']
                    ];
                    $basket['promoCode']['signature'] = $checkResult['promocodeSignature'];
                    $basket['promoCode']['discountNumber'] = $checkResult['promocodeDiscountTotal'];
                    $coupon = Coupon::create($couponBody);
                    Log::debug($coupon);
                }
                /* Формирование тела инвойса */
                $paymentInfo = [
                    'payment_method_types' => ['card'],
                    'line_items' => $stripeFormattedItems,
                    'mode' => 'payment',
                    'payment_intent_data' => ['receipt_email' => $user->email],
                    'success_url' => config('app.site_url') . '/successful-payment?session_id={CHECKOUT_SESSION_ID}&platform=stripe',
                    'cancel_url' => config('app.site_url') . '/basket',
                ];
                /* Если был применен промокод и затем создан купон stripe добавляем в тело инвойса поле discounts */
                if (isset($coupon)) {
                    $paymentInfo['discounts'] = [[
                        'coupon' => $coupon->id,
                    ]];
                }
                /* Создание сессии stripe */
                /* https://stripe.com/docs/api/checkout/sessions/create выбрать PHP в селекторе языков */
                $session = Session::create($paymentInfo);

                /* Добавление записи в базу */
                $invoice = Invoice::firstOrNew([
                    ['user_id', $user->id],
                    ['basket', json_encode($request->all())]
                ]);
                $invoice->fill([
                    'user_id' => $user->id,
                    'method' => 'stripe',
                    'session_id' => $session->id,
                    'basket' => $basket,
                    'price' => $cost,
                    'currency' => $currency['name'],
                    'paid_as_company' => $payAsCompany
                ]);
                $invoice->save();

                return response()->json(['id' => $session->id]);
            } else {
                response()->json($checkResult, 406);
            }
        }
        return response()->json(['success' => false], 406);
    }

    /* Cоздание сессии paypal. Начало платежа и формирование форматированных под paypal элементов корзины находится на фронте
        (src/app/site/inc/paypal/paypal.component.ts)
    */
    public function paypalCreateSession (Request $request, string $sessionId)
    {
        $payAsCompany = $request->get('payAsCompany');
        $basket = $request->get('basket');
        $user = User::findOrFail($basket['userId']);
        $items = $basket['items'];
        $cost = $basket['cost'];
        $currency = $basket['currency'];
        $promoCode = $basket['promoCode'];
        if (is_array($items) && $sessionId) {
            /* Проверяются на валидность элементы корзины */
            $checkResult = $this->checkItems($items, $user, $promoCode, $currency);
            if ($checkResult['confirmed'] === true) {
                /* Найти инвойс либо создать новый если не найден в базе */
                $invoice = Invoice::firstOrNew([
                    ['user_id', $user->id],
                    ['basket', json_encode($request->all())]
                ]);
                /* Обновление инвойса данными корзины */
                $invoice->fill([
                    'user_id' => $user->id,
                    'method' => 'paypal',
                    'session_id' => $sessionId,
                    'basket' => $basket,
                    'price' => $cost,
                    'currency' => $currency['name'],
                    'paid_as_company' => $payAsCompany
                ]);
                $invoice->save();

                return response()->json(['id' => $sessionId]);
            } else {
                response()->json($checkResult, 406);
            }
        }
        return response()->json(['success' => false], 406);
    }

    /* Подтверждение платежа после оплаты */
    public function confirmPayment (string $paymentPlatform, Request $request)
    {
        /* Query param session_id обязателен для обоих систем stripe и paypal, order_id только для paypal */
        $sessionId = $request->get('session_id');
        $orderId = $request->get('order_id');
        $success = false;
        $message = 'Session id doesn\'t specify';
        Log::debug('CONFIRM PAYMENT');
        Log::debug($sessionId);
        Log::debug($orderId);
        if ($sessionId) {
            /* Получение инвойса из базы по уникальному session_id, созданного на этапе создания сессии. */
            $invoice = Invoice::where('session_id', $sessionId)->firstOrFail();
            /* 0 - means unpaid, 1 - means already paid and processed */
            if ($invoice->state === 0) {
                $isPaymentApproved = false;
                if ($invoice->method === 'stripe') {
                    /* Получение и подтверждение данных по session_id инвойса в базе stripe */
                    $stripe = new StripeClient(config('stripe')[config('stripe.mode')]['secret']);
                    $sessionObject = $stripe->checkout->sessions->retrieve($sessionId, []);
                    $isPaymentApproved = $sessionObject['payment_status'] === 'paid';
                    Log::debug(json_encode($sessionObject));
                } elseif ($invoice->method === 'paypal' && $orderId) {
                    /* Получение и подтверждение данных по order_id инвойса в базе paypal */
                    $client = new PayPalHttpClient(new ProductionEnvironment(
                        config('paypal')[config('paypal.mode')]['id'],
                        config('paypal')[config('paypal.mode')]['secret']
                    ));
                    $request = new OrdersCaptureRequest($orderId);
                    $request->prefer('return=representation');
                    $sessionObject = json_decode(json_encode($client->execute($request)->result), true);
                    $isPaymentApproved = $sessionObject['status'] === 'COMPLETED';
                    Log::debug(json_encode($sessionObject));
                }
                if ($isPaymentApproved && is_array($invoice->basket)) {
                    /* объект сессии(ответ из платежных систем по платежу */
                    $invoice->session_object = $sessionObject;
                    /* Если инвойс и его платеж успешно проверены, то происходит добавление Ticket, UserTopic и UserCourse
                    *  для соответственных user - оплачиваемый элемент */
                    $response = $this->handlePayment($invoice->basket, $invoice->id);
                    /* state
                     *  0 - unpaid
                     *  1 - paid and successfully processed
                     *  3 - paid but process errored
                     */
                    if ($response['success'] === true) {
                        $invoice->state = 1;
                    } else {
                        $invoice->state = 3;
                    }
                    /* Обновление  записи инвойса с новым статусом и объектом сессии */
                    $invoice->save();
                    return response()->json($response);
                } else {
                    $message = 'Unpaid or incorrect data';
                }
            } elseif ($invoice->state === 1) {
                $success = true;
                $message = 'Already paid and processed';
            }
        }
        Log::debug('NOT PROCESSED');
        Log::debug($message);
        return response()->json(['success' => $success, 'message' => $message]);
    }

    public function getStripeFormattedItems($items, $currencyName): array
    {
        $totalCost = 0;
        $formatted = [];
        foreach ($items as $item) {
            $new = null;
            switch ($item['type']) {
                case 'video-course':
                    $course = Course::find($item['paymentItem']['id']);
                    $new = [
                        'price_data' => [
                            'currency' => $currencyName ?? 'eur',
                            'product_data' => [
                                'name' => 'Video course - '.$course->title
                            ],
                            'unit_amount' => ($course->discount_price ?? $course->actual_price) * 100,
                        ],
                        'quantity' => $item['count']
                    ];
                    if ($course->poster_url) {
                        $new['price_data']['product_data']['images'] = [config('app.prod_url').'/'.$course->poster_url];
                    }
                    $totalCost += $course->discount_price ?? $course->actual_price;
                    break;
                case 'video-topic':
                    $topic = Topic::find($item['paymentItem']['id']);
                    $new = [
                        'price_data' => [
                            'currency' => $currencyName ?? 'eur',
                            'product_data' => [
                                'name' => 'Video topic - '.$topic->title
                            ],
                            'unit_amount' => ($topic->discount_price ?? $topic->actual_price) * 100,
                        ],
                        'quantity' => $item['count']
                    ];
                    if ($topic->poster_url) {
                        $new['price_data']['product_data']['images'] = [config('app.prod_url').'/'.$topic->poster_url];
                    }
                    $totalCost += $topic->discount_price ?? $topic->actual_price;
                    break;
                case 'live-course':
                    $liveCourse = Event::find($item['item']['id']);
                    $date = Date::find($item['paymentItem']['id']);
                    $new = [
                        'price_data' => [
                            'currency' => $currencyName ?? 'eur',
                            'product_data' => [
                                'name' => 'Live course - '.$liveCourse->title
                                    .'. Date: '.date("d F Y", strtotime($date->start))
                                    .'. Language: '.$date->lang
                            ],
                            'unit_amount' => ($liveCourse->discount_price ?? $liveCourse->actual_price) * 100,
                        ],
                        'quantity' => $item['count']
                    ];
                    if ($liveCourse->poster_url) {
                        $new['price_data']['product_data']['images'] = [config('app.prod_url').'/'.$liveCourse->poster_url];
                    }
                    $totalCost += $liveCourse->discount_price ?? $liveCourse->actual_price;
                    break;
                default:
                    break;
            }
            if ($new) {
                array_push($formatted, $new);
            }
        }
        return ['items' => $formatted, 'totalCost' => $totalCost];
    }

    /* Букинг лив курса (Event) */
    public function handleBooking(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->get('userId'));
        /* Обработка запроса букинга */
        $result = $this->processItemBooking($request, $user);
        $result = array_merge($result, ['userId' => $user->id, 'operation' => 'booking', 'type' => 'live-course']);
        return response()->json($result);
    }

    /* Задание количества мест, которые юзер хочет забронировать при букинге лив курса(Event) */
    public function setBookingItemCount(Request $request): JsonResponse
    {
        $user = Auth::user();
        $itemId = (int) $request->get('item')['id'];
        $count = (int) $request->get('count');
        $item = Date::findOrFail($itemId);
        $ticket = Ticket::where([
            ['user_id', $user->id],
            ['date_id', $itemId]
        ])->orderBy('id', 'desc')->firstOrFail();
        if ($ticket->is_canceled === 1) {
            $result = ['success' => false, 'message' => 'The booking was canceled for the course for this date.', 'failedItem' => (object) [
                'type' => 'live-course',
                'paymentItem' => (object) ['id' => $itemId]
            ]];
            return response()->json($result, 406);
        }
        if ($ticket->is_purchased === 1) {
            $result = ['success' => false, 'message' => 'You have already purchased the course for this date.', 'failedItem' => (object) [
                'type' => 'live-course',
                'paymentItem' => (object) ['id' => $itemId]
            ]];
            return response()->json($result, 406);
        }
        $increment = $count - $ticket->count;
        $result = ['success' => true, 'message' => 'ok'];
        if ($increment > 0) {
            if ($item->seats_vacant >= $increment) {
                $ticket->count = $count;
                $ticket->save();
                /* Recalculate seats number for date(event) */
                (new UpdateTotalCount())->updateDateSeats($itemId);
            } else {
                $result = ['success' => false, 'message' => 'No vacant seats for this date'];
            }
        } elseif ($increment < 0) {
            $ticket->count = $count;
            $ticket->save();
            /* Recalculate seats number for date(event) */
            (new UpdateTotalCount())->updateDateSeats($itemId);
        } else {
            $result = ['success' => false, 'message' => 'The same count'];
        }
        return response()->json($result);
    }

    public function processItemBooking($request, $user)
    {
        $itemId = (int) $request->get('item')['id'];
        $itemProgress = null;
        foreach ($user->dates as $date) {
            if ((int) $date->id === $itemId) {
                $itemProgress = $date;
                break;
            }
        }
        $itemDate = Date::with(['event'])->findOrFail($itemId);
        $count = (int) $request['count'];
        if ($itemDate->seats_vacant >= $count || !is_null($itemProgress)) {
            /* Непосредственно бронирование(создание pivot модели Ticket для User - Date) курса */
            $body = [$itemId => [
                'count' => $count,
                'recipient_persons' => json_encode($request['recipientPersons']),
                'created_at' => Carbon::now()->toDateTimeString()
            ]];
            $user->dates()->attach($body);
            /* Уменьшаем счетчик вакантных мест для live-курса(event) */
            /* Recalculate booked and purchased seats number for live-course(event) */
            (new UpdateTotalCount())->updateDateSeats($itemId);

            $itemDate = Date::with(['event'])->find($itemId);
            SendCmnEmail::dispatch($user->email, 'Booking', 'email.booking', $itemDate, $user);
            $result = [
                'success' => true, 'message' => 'Seats have been reserved successfully! Items were added to the basket.',
                'item' => $itemDate, 'count' => $count, 'recipientPersons' => $request['recipientPersons']
            ];
        } else {
            $result = ['success' => false, 'message' => 'No vacant seats for this date'];
        }
        return $result;
    }

    /* Получение платных элементов(Ticket, UserTopic, UserCourse) без оплаты. Например при применении промокода в 100% */
    public function getItemsFree(Request $request)
    {
        return response()->json($this->handlePayment($request->all(), null, true));
    }

    /* После подтверждения платежа, обработка корзины */
    public function handlePayment(array $basket, int $invoiceId = null, bool $isFree = false): array
    {
        $user = User::findOrFail($basket['userId']);
        $items = $basket['items'];
        $promoCode = $basket['promoCode'];
        $currency = $basket['currency'];
        $result = ['userId' => $user->id, 'operation' => 'payment'];
        $paidItems = [];
        if (is_array($items)) {
            /* Проверка элементов корзины на валидность */
            $checkResult = $this->checkItems($items, $user, $promoCode, $currency);
            if ($checkResult['confirmed'] === true) {
                /* Проверка - если элементы пытаются получить бесплатно, но функция checkItems() насчитала,
                 * что промокод не покрывает всю стоимость,
                 * возвращается сообщение о невыполненном платеже
                 * */
                if ($isFree && !$checkResult['free']) {
                    return array_merge($result, [
                        'success' => false,
                        'message' => 'Getting items free is not allowed. Try apply your promocode again.'
                    ]);
                }
                $prefixes = [];
                /* Префиксы платежей по тз заказчика.
                 * OPNTK Nr. - лив курсы (Event), OPNTMO Nr. - видео курсы и топики (Course, Topic)
                 * */
                foreach ($items as $item) {
                    array_push($prefixes, str_starts_with($item['type'], 'live') ? 'OPNTK Nr. ' : 'OPNTMO Nr. ');
                    array_push($paidItems, $this->processItem($item, $user, $invoiceId));
                }
                Log::debug(json_encode(array_values(array_unique($prefixes))));
                /* Обработка применения промокода к конкретному юзеру */
                $this->applyPromoCode(is_null($promoCode) ? -1 : $promoCode['id'], $user);
                $invoice = Invoice::find($invoiceId);
                if ($invoice) {
                    /* Получение по порядку количественного значения платежа(не учитываются полученные бесплатно элементы)
                     * вместе с префиксом
                     * например: OPNTMO Nr. 5
                     * запись этого значения в поле additional_data и обновление инвойса с этим значением
                     * */
                    $invoice->additional_data = $this->getInvoiceIdName(array_values(array_unique($prefixes)));
                    $invoice->save();
                    /* Добавление в очередь job генерации файлов(excel, pdf) инвойса,
                     * и указание на необходимость отправки емэйла на почту юзеру о успешной оплате(третий параметр в функции)
                     * */
                    GenerateCompanyInvoiceFile::dispatch($user->id, $invoiceId, true, $paidItems);
//                    SendCmnEmail::dispatch($user->email, 'Payment', 'email.successful_payment', $paidItems, $user);
                } else {
                    SendCmnEmail::dispatch($user->email, 'Payment', 'email.successful_payment', $paidItems, $user);
                }
                $result = array_merge($result, [
                    'success' => true,
                    'message' => $checkResult['message'],
                    'items' => $paidItems
                ]);
            } else {
                $result = array_merge($result, [
                    'success' => false,
                    'message' => $checkResult['message']
                ]);
            }
        }
        return $result;
    }

    /* Применение промокода к определенному юзеру(инкрементация usage_count и добавление pivot UserPromocode,
     * проверка на которое предотвращает использование промокода несколько раз)
     * */
    public function applyPromoCode(int $id, User $user)
    {
        $code = Promocode::find($id);
        if (!is_null($code)) {
            if ($user->promoCodes->contains($id)) {
                $pivot = $user->promoCodes()->findOrFail($id)->pivot;
                $body = [$id => [
                    'applied_count' => ((int) $pivot->applied_count + 1)
                ]];
                $user->promoCodes()->sync($body, false);
            } else {
                $user->promoCodes()->attach($id);
            }
            /* Увеличиваем счетчик использования промокода */
            $this->incrementCount(
                'Promocode',
                $id,
                ['id'],
                'usage_count'
            );
        }
    }

    /* Проверка корзины на валидгность */
    public function checkBasket(Request $request)
    {
        $user = User::findOrFail($request->get('userId'));
        $items = $request->get('items');
        $currency = $request->get('currency');
        $promoCode = $request->get('promoCode');
        return response()->json($this->checkItems($items, $user, $promoCode, $currency));
    }

    public function validatePromocode($promoCode): bool
    {
        $valid = true;
        if ($promoCode) {
            if ($promoCode['discount_type'] === 'percent' && ($promoCode['discount'] <= 0 || $promoCode['discount'] > 100))  {
                $valid = false;
            }
            if ($promoCode['discount_type'] === 'numeric' && $promoCode['discount'] <= 0)  {
                $valid = false;
            }
        }
        return $valid;
    }

    public function checkItems($items, $user, $promoCode, $currency): array
    {
        $result = ['confirmed' => true, 'message' => 'ok', 'free' => false];
        $totalPrice = 0;
        $promocode = Promocode::find($promoCode['id'] ?? null);
        $promocodeType = $promocode ? $promocode->discount_type : null;
        $promocodeSubject = $promocode ? $promocode->subject : null;
        $promocodeDiscountTotal = 0;
        $promocodeSignature = '';
        if ($promocode) {
            if (!$this->validatePromocode($promocode)) {
                $result['confirmed'] = false;
                $result['message'] = 'Promocode is invalid';
                return $result;
            }
            $promocodeSignature .= $promocode->code.' - '.$promocode->discount;
        }
        foreach ($items as $item) {
            try {
                $this->checkItem($item['type'], $item['paymentItem']['id'], $user);
            } catch (\Exception $exception) {
                Log::debug('CHECK ITEMS EXCEPTION');
                Log::debug($exception->getMessage());
                $result['confirmed'] = false;
                $result['message'] = $exception->getMessage();
                $result['failedItem'] = $item;
                break;
            }
            $itemCost = ($item['item']['discount_price'] ?: $item['item']['actual_price']) * $item['count'];
            $totalPrice += $itemCost;
            if ($promocodeSubject) {
                $type = null;
                switch ($promocode->subject_type) {
                    case 'App\Course':
                        $type = 'video-course';
                        break;
                    case 'App\Topic':
                        $type = 'video-topic';
                        break;
                    case 'App\Event':
                        $type = 'live-course';
                        break;
                }
                if ($promocode->subject_id === $item['item']['id'] && $item['type'] === $type) {
                    if ($promocodeType === 'percent') {
                        $promocodeSignature .= '%';
                        $promocodeDiscountTotal = $itemCost * $promocode->discount / 100;
                    } elseif ($promocodeType === 'numeric') {
                        $promocodeSignature .= $currency['sign'];
                        $promocodeDiscountTotal = $itemCost - $promocode->discount > 0 ? $promocode->discount : $itemCost;
                    }
                    $promocodeSignature .= ' for '.$promocodeSubject->title.' '.ucfirst($type);
                }
            }
        }

        if ($promocode && !$promocodeSubject) {
            if ($promocodeType === 'percent') {
                $promocodeSignature .= ' %';
                $promocodeDiscountTotal = $totalPrice * $promocode->discount / 100;
            } elseif ($promocodeType === 'numeric') {
                $promocodeSignature .= ' '.$currency['sign'];
                $promocodeDiscountTotal = $promocode->discount;
            }
        }
        if ($promocodeDiscountTotal > 0) {
            $result['promocodeSignature'] = $promocodeSignature;
            $result['promocodeDiscountTotal'] = $promocodeDiscountTotal;
        }
        if ($promocodeDiscountTotal >= $totalPrice) {
            $result['free'] = true;
        }
        return $result;
    }

    public function checkItem($type, $itemId, $user)
    {
        switch ($type) {
            case 'video-course':
                $course = Course::with(['topics'])->findOrFail($itemId);
                $courseProgress = $user->courses()->find($course->id) ? $user->courses()->find($course->id)->pivot : null;
                if ($courseProgress) {
                    if ($courseProgress->is_purchased === 1) {
                        throw new \Exception('This course has been already purchased');
                    }
                }
                break;
            case 'video-topic':
                $topic = Topic::findOrFail($itemId);
                $courseRelatedId = $topic->course;
                if (is_null($courseRelatedId)) {
                    throw new \Exception('Related to the topic course hasn\'t been found');
                }
                $topicProgress = $user->topics()->find($topic->id) ? $user->topics()->find($topic->id)->pivot : null;
                if ($topicProgress) {
                    if ($topicProgress->is_purchased === 1) {
                        throw new \Exception('This topic has been already purchased');
                    }
                }
                break;
            case 'live-course':
                $itemProgress = $user->dates()->where('date_id', $itemId)->first();
                $itemDate = Date::with(['event'])->findOrFail($itemId);
                if (is_null($itemProgress)) {
                    throw new \Exception('Live-course booking for the '.date("F d - ", strtotime($itemDate->start)).date("d", strtotime($itemDate->end)).' doesn\'t exist. Please, book the live-course first');
                } else {
                    if ($itemProgress->pivot->is_canceled == 1) {
                        throw new \Exception('Live-course booking for the '.date("F d - ", strtotime($itemDate->start)).date("d", strtotime($itemDate->end)).' has been canceled. Please, book the live-course again');
                    }
                    if ($itemProgress->pivot->is_purchased === 1) {
                        throw new \Exception('The course for this date has been already purchased');
                    }
                }
                if ($itemDate->seats_vacant >= 0) {
                    $event = $itemDate->event;
                    if (is_null($event)) {
                        throw new \Exception('Related to the date event hasn\'t been found');
                    }
                } else {
                    throw new \Exception('No vacant seats for this date');
                }
                break;
            default:
                throw new \Exception('The requested item category hasn\'t been found');
        }
        return true;
    }

    public function processItem($item, User $user, $invoiceId)
    {
        $itemId = (int) $item['paymentItem']['id'];
        $type = $item['type'];
        $result = null;
        switch ($type) {
            case 'video-course':
                if ($user->courses->contains($itemId)) {
                    /* Обновление created_at в обновляемом UserCourse необходимо для правильной сортировки в orders */
                    $body = [$itemId => [
                        'is_purchased' => true,
                        'invoice_id' => $invoiceId,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]];
                    $user->courses()->sync($body, true);
                } else {
                    $body = [$itemId => [
                        'is_purchased' => true,
                        'invoice_id' => $invoiceId,
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]];
                    $user->courses()->attach($body);
                }

                /* Увеличиваем счетчик покупок для курса*/
                $this->incrementCount(
                    'Course',
                    $itemId,
                    ['id', 'route', 'name'],
                    'purchase_count'
                );
                $itemCourse = Course::with(['topics'])->findOrFail($itemId);
                $result = (object) ['type' => $type, 'itemId' => $itemId, 'item' => $itemCourse];
                break;
            case 'video-topic':
                if ($user->topics->contains($itemId)) {
                    /* Обновление created_at в обновляемом UserTopic необходимо для правильной сортировки в orders */
                    $body = [$itemId => [
                        'is_purchased' => true,
                        'invoice_id' => $invoiceId,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]];
                    $user->topics()->sync($body, true);
                } else {
                    $body = [$itemId => [
                        'is_purchased' => true,
                        'invoice_id' => $invoiceId,
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]];
                    $user->topics()->attach($body);
                }

                /* Увеличиваем счетчик покупок для топика*/
                $this->incrementCount(
                    'Topic',
                    $itemId,
                    ['id', 'route', 'name'],
                    'purchase_count'
                );

                /* Привязываем курс, в котором находится текущий топик, к пользователю */
                $courseRelatedId = Topic::findOrFail($itemId)->course->id;

                /* Не использовать здесь $user->courses->contains($courseRelatedId).
                 * При использовании contains() в UserCourse появляются дубликаты при покупке нескольких топиков одновременно.
                 * Чего быть не должно.
                 * */
                $userCoursePivot = UserCourse::where([
                    ['user_id', $user->id],
                    ['course_id', $courseRelatedId]
                ])->first();
                if (!$userCoursePivot) {
                    $user->courses()->attach([$courseRelatedId => [
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]]);
                }
                $itemTopic = Topic::with(['course', 'lessons'])->findOrFail($itemId);
                $result = (object) ['type' => $type, 'itemId' => $itemId, 'item' => $itemTopic];
                break;
            case 'live-course':
                $itemDate = Date::with(['event'])->findOrFail($itemId);
                /* Покупка курса */
                if ($user->dates->contains($itemId)) {
//               $body = [$itemId => [
//                  'is_purchased' => true,
//                  'invoice_id' => $invoiceId,
//                  'count' => $item['count'],
//                  'recipient_persons' => json_encode($item['recipientPersons']),
//                  'updated_at' => Carbon::now()->toDateTimeString()
//               ]];
                    $body = [
                        'is_purchased' => true,
                        'is_reminded' => false,
                        'invoice_id' => $invoiceId,
                        'count' => $item['count'],
                        'recipient_persons' => $item['recipientPersons'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ];
                    $ticket = Ticket::where([
                        ['user_id', $user->id],
                        ['date_id', $itemId],
                        ['is_purchased', '!=', 1]
                    ])->orderBy('id', 'desc')->first();
                    $ticket->update($body);
//               $user->dates()->sync($body, false);
                } else {
                    $body = [$itemId => [
                        'is_purchased' => true,
                        'is_reminded' => false,
                        'invoice_id' => $invoiceId,
                        'count' => $item['count'],
                        'recipient_persons' => json_encode($item['recipientPersons']),
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]];
                    $user->dates()->attach($body);
                    /* Уменьшаем счетчик вакантных мест для live-курса(event, date),
                    * если до этого место было не забронировано */
                    $this->decrementCount(
                        'Date',
                        $itemId,
                        ['id'],
                        'seats_vacant',
                        (int) $item['count']
                    );
                }
                $eventId = $itemDate->event->id;
                /* Увеличиваем счетчик покупок для live-курса(event) */
                $this->incrementCount(
                    'Event',
                    $eventId,
                    ['id', 'route', 'name'],
                    'bought_tickets_count',
                    (int) $item['count']
                );
                if (is_array($item['recipientPersons'])) {
                    foreach ($item['recipientPersons'] as $person) {
                        if ($person['email'] && $person['email'] !== $user->email) {
                            SendCmnEmail::dispatch(
                                $person['email'],
                                'Payment', 'email.live_course_purchase_recipient',
                                $itemDate,
                                $person,
                                $user
                            );
                        }
                    }
                }
                $result = (object) ['type' => $type, 'itemId' => $itemId, 'item' => $itemDate];
                break;
            default:
                break;
        }
        return $result;
    }

    /* Получение по порядку количественного значения платежа(не учитываются полученные бесплатно элементы)
     * вместе с префиксом
     * например: OPNTMO Nr. 5
     * */
    private function getInvoiceIdName(array $prefixes): string
    {
        $result = '';
        $len = count($prefixes) - 1;
        foreach ($prefixes as $idx => $prefix) {
            $count = 0;
            $result .= $prefix;
            if ($prefix === 'OPNTK Nr. ') {
                $count += count(Ticket::where('is_purchased', 1)->get());
            } elseif ($prefix === 'OPNTMO Nr. ') {
                $count += count(UserCourse::where('is_purchased', 1)->whereNotNull('invoice_id')->get());
                $count += count(UserTopic::where('is_purchased', 1)->whereNotNull('invoice_id')->get());
            } else {
                $count += count(UserCourse::where('is_purchased', 1)->get()) + count(UserTopic::where('is_purchased', 1)->get());
            }
            $result .= $count;
            if ($idx < $len) {
                $result .= ', ';
            }
        }
        return $result;
    }

}
