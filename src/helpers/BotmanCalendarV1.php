<?php

namespace Uasoft\BotmanCalendar\helpers;

use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class BotmanCalendarV1
{
    protected $calendar;

    public function __construct($year = null, $month = null)
    {
        $this->makeCalendar($year, $month);
    }

    public function makeCalendar($year = null, $month = null)
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
        $date = new \DateTime('now');
        $year = is_null($year) ? $date->format('Y') : $year;
        $month = is_null($month) ? $date->format('m') : $month;

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

        $this->calendar = $keyboard;
    }

    public function toArray()
    {
        return $this->calendar->toArray();
    }
}
