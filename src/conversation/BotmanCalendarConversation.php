<?php

namespace Uasoft\BotmanCalendar\conversation;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class BotmanCalendarConversation extends Conversation
{
    public $callback;
    public $message = 'Select Date';

    protected $current_month;
    protected $current_year;
    protected $selected_date;

    protected $year_length = 15;
    protected $start_year;
    protected $end_year;

    public function run()
    {
        $date = new \DateTime('now');
        $this->current_month = $date->format('m');
        $this->current_year = $date->format('Y');
        $this->start_year = $this->current_year;
        $this->askDate();
    }

    public function createDaysOfMonth($year = null, $month = null)
    {
        // DECLARE PARAMETER
        $days = [
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday',
        ];
        $year = $this->current_year;
        $month = $this->current_month;

        $selected_date = \DateTime::createFromFormat('Ym', $year.$month);

        $month_year = $selected_date->format('F').' '.$year;
        $start = 1;
        $end = $selected_date->format('t'); // END OF MONTH
        $dates = range($start, $end);

        $complete_date = "$year-$month-$start";
        $datetime = \DateTime::createFromFormat('Y-m-d', $complete_date);
        $first_day_of_month = $datetime->format('l');
        $first_day_of_month_index = null;

        // CREATE KEYBOARD
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)->resizeKeyboard();

        // SET Title
        $keyboard->addRow(KeyboardButton::create($month_year)->callbackData("$year-$month"));

        // SET Day
        $keyboard->addRow(
            KeyboardButton::create('Mon')->callbackData(1),
            KeyboardButton::create('Tue')->callbackData(2),
            KeyboardButton::create('Wed')->callbackData(3),
            KeyboardButton::create('Thu')->callbackData(4),
            KeyboardButton::create('Fri')->callbackData(5),
            KeyboardButton::create('Sat')->callbackData(6),
            KeyboardButton::create('Sun')->callbackData(7)
        );

        // SET OFFSET
        foreach ($days as $key => $value) {
            if (strtoupper($value) == strtoupper($first_day_of_month)) {
                $first_day_of_month_index = $key;
            }
        }
        $date_offset_size = $first_day_of_month_index - 1;

        $date_offset = [];
        for ($i = 1; $i <= $date_offset_size; ++$i) {
            $date_offset[] = ' ';
        }

        $dates = array_merge($date_offset, $dates);

        // SET TRAILING
        $month_size = count($dates);
        $month_week = ceil($month_size / 7); // ROUND UP
        $month_size_with_trailing = $month_week * 7;
        $trailing_size = $month_size_with_trailing - $month_size;
        $trailings = [];
        for ($i = 1; $i <= $trailing_size; ++$i) {
            $trailings[] = ' ';
        }

        $dates = array_merge($dates, $trailings);

        // SET 7 days in a row
        $weekly_dates = array_chunk($dates, 7);
        foreach ($weekly_dates as $weekly_date) {
            $keyboard->addRow(
                KeyboardButton::create($weekly_date[0])->callbackData("$year-$month-$weekly_date[0]"),
                KeyboardButton::create($weekly_date[1])->callbackData("$year-$month-$weekly_date[1]"),
                KeyboardButton::create($weekly_date[2])->callbackData("$year-$month-$weekly_date[2]"),
                KeyboardButton::create($weekly_date[3])->callbackData("$year-$month-$weekly_date[3]"),
                KeyboardButton::create($weekly_date[4])->callbackData("$year-$month-$weekly_date[4]"),
                KeyboardButton::create($weekly_date[5])->callbackData("$year-$month-$weekly_date[5]"),
                KeyboardButton::create($weekly_date[6])->callbackData("$year-$month-$weekly_date[6]")
            );
        }

        // SET Navigation Button
        $keyboard->addRow(KeyboardButton::create('<')->callbackData('-'), KeyboardButton::create('>')->callbackData('+'));

        return $keyboard;
    }

    public function createMonthsOfYear($year = null)
    {
        // DECLARE PARAMETER
        $months = [
            ['key' => '1', 'label' => 'January'],
            ['key' => '2', 'label' => 'February'],
            ['key' => '3', 'label' => 'March'],
            ['key' => '4', 'label' => 'April'],
            ['key' => '5', 'label' => 'May'],
            ['key' => '6', 'label' => 'June'],
            ['key' => '7', 'label' => 'July'],
            ['key' => '8', 'label' => 'August'],
            ['key' => '9', 'label' => 'September'],
            ['key' => '10', 'label' => 'October'],
            ['key' => '11', 'label' => 'November'],
            ['key' => '12', 'label' => 'December'],
        ];

        $year = $this->current_year;

        // CREATE KEYBOARD
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)->resizeKeyboard();

        // SET Title
        $keyboard->addRow(KeyboardButton::create($year)->callbackData($year));

        // SET 3 month in a row
        $months_rows = array_chunk($months, 3);
        foreach ($months_rows as $months_row) {
            $keyboard->addRow(
                KeyboardButton::create($months_row[0]['label'])->callbackData($months_row[0]['key']),
                KeyboardButton::create($months_row[1]['label'])->callbackData($months_row[1]['key']),
                KeyboardButton::create($months_row[2]['label'])->callbackData($months_row[2]['key'])
            );
        }

        // SET Navigation Button
        $keyboard->addRow(KeyboardButton::create('<')->callbackData('-'), KeyboardButton::create('>')->callbackData('+'));

        return $keyboard;
    }

    public function createYears()
    {
        $year_size = $this->year_length;
        $date = new \DateTime('now');
        $start_year = $this->start_year;
        $end_year = $start_year - $year_size + 1;
        $this->end_year = $end_year;
        $years = range($start_year, $end_year, -1);

        // CREATE KEYBOARD
        $keyboard = Keyboard::create(Keyboard::TYPE_INLINE)->resizeKeyboard();

        // SET 3 year in a row
        $years_rows = array_chunk($years, 3);
        foreach ($years_rows as $years_row) {
            $keyboard->addRow(
                KeyboardButton::create($years_row[0])->callbackData($years_row[0]),
                KeyboardButton::create($years_row[1])->callbackData($years_row[1]),
                KeyboardButton::create($years_row[2])->callbackData($years_row[2])
            );
        }

        // SET Navigation Button
        $keyboard->addRow(KeyboardButton::create('<')->callbackData('-'), KeyboardButton::create('>')->callbackData('+'));

        return $keyboard;
    }

    public function toArray()
    {
        return $this->calendar->toArray();
    }

    public function askDate()
    {
        $calendar = $this->createDaysOfMonth();
        $this->ask($this->message, function (Answer $answer) {
            $date = $answer->getValue();
            if (strlen($date) == 10 || strlen($date) == 9 || strlen($date) == 8) {
                $this->selected_date = $date;
                $this->runCallBack();
            } elseif (strlen($date) == 7 || strlen($date) == 6) {
                $this->askMonth();
                $this->deleteMessage(
                    $answer->getMessage()->getPayload()['chat']['id'],
                    $answer->getMessage()->getPayload()['message_id']
                );
            } else {
                if ($date == '+') {
                    if ($this->current_month == 12) {
                        $this->current_month = 1;
                        ++$this->current_year;
                    } else {
                        ++$this->current_month;
                    }
                } elseif ($date == '-') {
                    if ($this->current_month == 1) {
                        $this->current_month = 12;
                        --$this->current_year;
                    } else {
                        --$this->current_month;
                    }
                }
                $this->askDate();
                $this->deleteMessage(
                    $answer->getMessage()->getPayload()['chat']['id'],
                    $answer->getMessage()->getPayload()['message_id']
                );
            }
        }, $calendar->toArray());
    }

    public function askMonth()
    {
        $calendar = $this->createMonthsOfYear();
        $this->ask('Select Month', function (Answer $answer) {
            $date = $answer->getValue();
            if ($date == '+') {
                ++$this->current_year;
                $this->askMonth();
            } elseif ($date == '-') {
                --$this->current_year;
                $this->askMonth();
            } else {
                if (strlen($date) == 4) {
                    $this->askYear();
                } elseif (strlen($date) == 1 || strlen($date) == 2) {
                    $this->current_month = $date;
                    $this->askDate();
                } else {
                    $this->askMonth();
                }
            }
            $this->deleteMessage(
                $answer->getMessage()->getPayload()['chat']['id'],
                $answer->getMessage()->getPayload()['message_id']
            );
        }, $calendar->toArray());
    }

    public function askYear()
    {
        $calendar = $this->createYears();
        $this->ask('Select Year', function (Answer $answer) {
            $date = $answer->getValue();
            if ($date == '+') {
                $this->start_year = $this->start_year + $this->year_length;
                $this->askYear();
            } elseif ($date == '-') {
                $this->start_year = $this->end_year - 1;
                $this->askYear();
            } else {
                if (strlen($date) == 4) {
                    $this->current_year = $date;
                    $this->askMonth();
                } else {
                    $this->askYear();
                }
            }
            $this->deleteMessage(
                $answer->getMessage()->getPayload()['chat']['id'],
                $answer->getMessage()->getPayload()['message_id']
            );
        }, $calendar->toArray());
    }

    public function getSelectedDate()
    {
        return $this->selected_date;
    }

    public function runCallBack()
    {
        $this->say($this->selected_date);
        if (!is_null($this->callback)) {
            $callback = $this->callback;
            // $callback($date);
            serialize($callback($this->selected_date));
        }
    }

    private function deleteMessage($chat_id, $message_id)
    {
        $parameter = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ];
        $this->bot->sendRequest('deleteMessage', $parameter);
    }
}
