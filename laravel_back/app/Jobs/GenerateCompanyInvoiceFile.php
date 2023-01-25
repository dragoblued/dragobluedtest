<?php

namespace App\Jobs;

use App\Invoice;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;
use Numbers_Words;
use Stripe\Customer;
use Stripe\Stripe;

//use PhpOffice\PhpSpreadsheet\IOFactory;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class GenerateCompanyInvoiceFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $user;
    private $invoice;
    private $needSendEmail;
    private $paidItems;
    private $shouldGenerate;

    /**
     * Remove video files.
     *
     * @param int $userId
     * @param int $invoiceId
     * @param bool $needSendEmail
     * @param array $paidItems
     * @param bool $shouldGenerate
     */
    public function __construct(int $userId, int $invoiceId, bool $needSendEmail = false, array $paidItems = [], $shouldGenerate = true)
    {
        $this->user = User::findOrFail($userId);
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->needSendEmail = $needSendEmail;
        $this->paidItems = $paidItems;
        $this->shouldGenerate = $shouldGenerate;
    }

    /* Получение имени пользователя\компании */
    private function getCustomerName(User $user, Invoice $invoice): string
    {
        $customerName = $user->email;
        /* Если пользователь оплатил с опцией 'pay as company' */
        if ($invoice->paid_as_company === 1) {
            if (!is_null($user->company_info)) {
                $customerName = $user->company_info['company_name'];
            }
        } else {
            /* Если пользователь оплатил без этой опции */
            /* Если у пользователя указано имя в аккаунте */
            if ($user->name) {
                $customerName = $user->name.($user->surname ? ' '.$user->surname : '')." ({$user->email})";
            } elseif (strpos($invoice->additional_data, 'OPNTK') !== false) {
                /* Если у пользователя не указано имя в аккаунте, и оплачивается лив-курс,
                 * то имя берется из первого элемента массива recipientPersons(только для лив [Event]).
                 * Массив представляет собой пользователей, для которых этот курс покупается.
                 * В нормальных условиях, на первом месте массива должен быть сам пользователь,
                 * который и оплачивает курс.
                 * */
                $basket = $invoice->basket;
                $recipients = $basket['items'][0]['recipientPersons'];
                if ($recipients[0]) {
                    /* {name: string; email: string; phone: string;} */
                    $customerName = $recipients[0]['name'];
                }
            } else {
                /* Если у пользователя не указано имя в аккаунте, и оплачивается НЕ лив-курс,
                 * то имя берется из объекта сессии или api stripe или paypal
                 * */
                $sessionObj = $invoice->session_object;
                if ($invoice->method === 'paypal' && isset($sessionObj['payer']['name']['given_name'])) {
                    $customerName = $sessionObj['payer']['name']['given_name'].' '.$sessionObj['payer']['name']['surname']
                        ." ({$user->email})";
                } else if ($invoice->method === 'stripe' && isset($sessionObj['customer'])) {
                    Stripe::setApiKey(config('stripe')[config('stripe.mode')]['secret']);
                    /* https://stripe.com/docs/api/customers/retrieve выбрать PHP в селекторе языков */
                    $customer = Customer::retrieve($sessionObj['customer']);
                    $this->saveCustomerObject($invoice, $customer);
                    if ($customer['name'] || $customer['description']) {
                        $customerName = ($customer['name'] ?? $customer['description'])." ({$user->email})";
                    }
                }
            }
        }
        return $customerName;
    }

    /* Дополнение объекта сессии новыми данными о пользователе */
    private function saveCustomerObject(Invoice $invoice, $customerObj): void
    {
//       Log::debug(json_encode($customerObj));
        $sessionObj = $invoice->session_object;
        $sessionObj['customer_full'] = $customerObj;
        $invoice->session_object = $sessionObj;
        $invoice->save();
    }

    /**
     * Start job for empty video directory.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('GENERATE INVOICE FILE START');
        Log::debug($this->invoice->id);
        $fields = [];
        $basket = $this->invoice->basket;
        $promocode = $basket['promoCode'] ?? null;
        $promocodeAdded = false;
        /* Формирование полей для подстановки их в файл инвойса xlsx */
        $fields['invoice_id'] = $this->invoice->additional_data;
        $fields['company_name'] = $this->getCustomerName($this->user, $this->invoice);
        Log::debug($fields['company_name']);
        if ($this->invoice->paid_as_company === 1) {
            $fields['company_code'] = $this->user->company_info['company_code'] ?? '';
            $fields['company_address'] = $this->user->company_info['company_address'] ?? '';
            $fields['company_vat'] = $this->user->company_info['company_tax'] ?? '';
        } else {
            $fields['company_code'] = '';
            $fields['company_address'] = '';
            $fields['company_vat'] = '';
        }
        $fields['invoice_date'] = $this->invoice->created_at ? $this->invoice->created_at->format('Y-m-d H:i') : Carbon::now()->format('Y-m-d H:i');
        Log::debug('INVOICE_DATE');
        Log::debug($fields['invoice_date']);
        $itemsNum = count($basket['items']) + ($promocode ? 1 : 0);

        $arr = $itemsNum > 5 ? range(0, 9) : range(0, 4);
        foreach ($arr as $idx) {
            $item = $basket['items'][$idx] ?? null;
            $num = $idx + 1;
            if ($item) {
                $fields["item{$num}_desc"] = ucfirst(str_replace('-', ' ', $item['type'])).' - '.$item['item']['title'];
                $fields["item{$num}_amount"] = (int) $item['count'];
                $fields["item{$num}_price"] = (double) ($item['item']['discount_price'] ?? $item['item']['actual_price']);
                $fields["item{$num}_sum"] = (int) $item['count'] * ((double) ($item['item']['discount_price'] ?? $item['item']['actual_price']));
            } else {
                if ($promocode && !$promocodeAdded) {
                    $fields["item{$num}_desc"] = 'Promocode discount '.($promocode['signature'] ?? '');
                    $fields["item{$num}_amount"] = '1';
                    $fields["item{$num}_price"] = $promocode['discountNumber']
                        ?? ($promocode['discount_type'] === 'percent' ? $promocode['discount'].'%' : $promocode['discount']);
                    $fields["item{$num}_sum"] = isset($promocode['discountNumber']) ? ($promocode['discountNumber']
                        ? ($promocode['discountNumber'] * -1) : ((double) $basket['cost'] - (double) $basket['costWithoutDiscount']))
                        : ((double) $basket['cost'] - (double) $basket['costWithoutDiscount']);
                    $promocodeAdded = true;
                } else {
                    $fields["item{$num}_desc"] = '';
                    $fields["item{$num}_amount"] = '';
                    $fields["item{$num}_price"] = '';
                    $fields["item{$num}_sum"] = '';
                }
            }
        }
        $fields['total'] = $basket['cost'];
        $fields['total_int_words_lt'] = (new Numbers_Words())->toWords((int) $basket['cost'], 'lt');
        $fields['total_int_words_en'] = (new Numbers_Words())->toWords((int) $basket['cost']);
        $fields['total_decimal_words_lt'] = (new Numbers_Words())->toWords(
            (int) (explode('.', (string) (number_format((double) $basket['cost'], 2, '.', '')))[1] ?? 0),
            'lt'
        );
        $fields['total_decimal_words_en'] = (new Numbers_Words())->toWords(
            (int) (explode('.', (string) (number_format((double) $basket['cost'], 2, '.', '')))[1] ?? 0)
        );

        if (!$this->shouldGenerate) {
            return;
        }

        $this->saveExcel(storage_path("media/invoices_company/{$this->invoice->id}.xlsx"), $itemsNum, $fields);
        $this->convertExcelToPdf(
            storage_path("media/invoices_company/{$this->invoice->id}.xlsx"),
            storage_path("media/invoices_company")
        );

        /* Build attachment */
        $fileRoute = "media/invoices_company/{$this->invoice->id}.pdf";
        $fileUrl = "media/company_invoices/{$this->invoice->id}.pdf";
        /* Формирование приложения к письму файла инвойса */
        $attachment = [
            'attachmentPath' => storage_path($fileRoute),
            'attachmentName' => 'VAT invoice.pdf',
            'attachmentMime' => 'application/pdf'
        ];
        /* Если pdf файл по какой-либо причине не найден, попытка вставки файла xlsx */
        if (!File::exists($attachment['attachmentPath'])) {
            $fileRoute = "media/invoices_company/{$this->invoice->id}.xlsx";
            $fileUrl = "media/company_invoices/{$this->invoice->id}.xlsx";
            $attachment = [
                'attachmentPath' => storage_path($fileRoute),
                'attachmentName' => 'VAT invoice.xlsx',
                'attachmentMime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
        }

        $this->invoice->company_invoice_url = $fileUrl;
        $this->invoice->save();

        /* Отправка уведомления на почту вместе с только что сгенерированным инвойсом в поле $attachment
         * в зависимости от переданного значения $this->needSendEmail
         * */
        if ($this->needSendEmail) {
            SendCmnEmail::dispatch(
                $this->user->email,
                'Payment',
                'email.successful_payment',
                $this->paidItems,
                $this->user,
                null,
                $attachment
            );
        }
    }

    /* Генерация xlsx файла шаблона. ПРОБЛЕМА - в файл через раз подставляется переменная invoice_date.
     * Требуется выяснение причин, либо смена библиотеки
     * */
    private function saveExcel (string $path, int $itemsNum = 0, array $fields = [])
    {
        $params = [];
        foreach ($fields as $field => $value) {
            $params['{'. $field .'}'] = $value;
        }
        /* Если элментов корзины больше 5, то используется файл шаблона с 10 строками, иначе с 5 */
        $templatePath = $itemsNum > 5 ? storage_path('media/templates/company_invoice_template_10.xlsx')
            : storage_path('media/templates/company_invoice_template_5.xlsx');
        PhpExcelTemplator::saveToFile(
            $templatePath,
            $path,
            $params
        );
//      $spreadsheet = IOFactory::load($path);
//      foreach ([9, 10, 11, 12, 19, 20, 21, 22, 23] as $rowNum) {
//         $spreadsheet->getActiveSheet()->getRowDimension($rowNum)->setRowHeight(-1);
//      }
//      $writer = new Xlsx($spreadsheet);
//      $writer->save($path);
    }

    /* Конвертация файла xlsx в pdf с использованием библиотеки libreoffice,
     * путь в системе до которой должен быть указан в переменной env LIBREOFFICE_BINARIES
     * */
    private function convertExcelToPdf (string $pathSrc, string $folderDest)
    {
        /* Using system software libreoffice for converting */
        shell_exec(config('libreoffice.binaries')." --headless --convert-to pdf:calc_pdf_Export --outdir {$folderDest} {$pathSrc}");
    }

    public function failed($exception)
    {
        Log::debug($exception->getMessage());
    }
}
