<?php

namespace App\Http\Controllers\Api\Admin;

use App\Certificate;
use App\TestQuestion;
use App\TestResult;
use Illuminate\Http\Request;

class TestResultController extends AdminController
{
    public function __construct ()
    {
        $this->model = TestResult::class;
        $this->rules = [
            'test_id'   => 'required|integer',
            'user_id'   => 'required|integer'
        ];
    }

    public function show ($uniqueField, Request $request)
    {
        if (is_int($uniqueField)) {
            return $this->model::findOrFail($uniqueField);
        } else {
            return $this->model::where([
                ['test_id', (int) $request->testId],
                ['user_id', (int) $request->userId]
            ])->first();
        }
    }

    public function store (Request $request)
    {
        if($this->checkStore()) {
            $request->validate($this->rules);
            $request->merge([
                'status' => 'in-progress',
                'test_started_timestamp' => now(),
            ]);

            $item = $this->model::where([
                ['test_id', (int) $request->test_id],
                ['user_id', (int) $request->user_id]
            ])->first();

            if (is_null($item)) {
                $item = $this->model::create($request->all());
            } else {
                $request->merge([
                    'status' => 'in-progress',
                    'attempt_number' => $item->attempt_number + 1,
                    'test_started_timestamp' => now()
                ]);
                $item->fill($request->all());
                $item->save();
            }

            return $item;
        }

        return response()->json(['error' => 'Creating items in this class is forbidden'], 403);
    }

    protected function preUpdate (int $id, Request $request) {
        $this->setRule('test_id.required', "nullable");
        $this->setRule('user_id.required', "nullable");
    }

    public function finish ($id, Request $request)
    {
        $item = $this->model::where([
            ['test_id', (int) $request->testId],
            ['user_id', (int) $request->userId]
        ])->firstOrFail();
        $marks = $this->getUserMark($item);
        $minPercent = $item->test->minimum_percentage;
        $userMark = $marks[1];
        $userMarkPercent = ceil($marks[1] / $marks[0] * 100);

        $allMarks = $item->obtained_marks;
        if (!is_array($allMarks)) {
            $allMarks = [];
        }
        array_push($allMarks, $userMark);
        $maxMark = max($allMarks);
        $maxMarkPercent = ceil($maxMark / $marks[0] * 100);
        if ($userMark >= $maxMark) {
            $data = [
                'obtained_marks' => $allMarks,
                'max_mark' => $userMark,
                'max_mark_percent' => $userMarkPercent,
                'result' => $userMarkPercent > $minPercent ? 'passed' : 'failed',
                'status' => 'finished'
            ];
        } else {
            $data = [
                'obtained_marks' => $allMarks,
                'status' => 'finished'
            ];
        }

        if ($maxMarkPercent >= $minPercent) {
            $this->checkSertificateCreated($item->test_id, $item->user_id);
        }

        $item->fill($data);
        $item->save();

        return $item;
    }

    private function getUserMark ($result)
    {
        $assocArr = json_decode($result->answer_script);
        $userMark = 0;
        $totalMark = 0;
        foreach ($assocArr as $id => $value) {
            $question = TestQuestion::findOrFail((int) $id);
            $totalMark += $question->mark;
            $correctAnswers = $question->correct_answers;
            if ($question->type === 'single-choice') {
                if ((int) $correctAnswers[0] === $value) {
                    $userMark += $question->mark;
                }
            } elseif ($question->type === 'multiple-choice' || $question->type === 'fill-in-the-blanks') {
                foreach ($correctAnswers as $key => $corAns) {
                    if ($question->type === 'multiple-choice') {
                        if (!in_array((int) $corAns, $value)) {
                            break 1;
                        }
                    } else {
                        if (!in_array( $corAns, $value)) {
                            break 1;
                        }
                    }

                    if ($key === sizeof($correctAnswers) - 1) {
                        $userMark += $question->mark;
                    }
                }
            }
        }
        return [$totalMark, $userMark];
    }

    private function checkSertificateCreated (int $testId, int $userId) {
        $certificate = Certificate::where([
            ['test_id', $testId],
            ['user_id', $userId]
        ])->first();

        if (is_null($certificate)) {
            app('App\Http\Controllers\Api\Admin\CertificateController')->store(new Request([
                'test_id' => $testId,
                'user_id' => $userId,
            ]));
        }
    }

}
