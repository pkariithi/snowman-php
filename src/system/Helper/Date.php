<?php

namespace Helper;

use DateTime;
use DatePeriod;
use DateInterval;

class Date {

  public static $seconds = [
    'minute' => 60,
    'half_hour' => 30 * 60,
    'hour' => 60 * 60,
    'day' => 24 * 60 * 60,
    'two_days' => 2 * 24 * 60 * 60,
    'week' => 7 * 24 * 60 * 60,
    'month' => 30 * 24 * 60 * 60,
    'year' => 12 * 30 * 24 * 60 * 60,
  ];

  public static function isFuture($datetime = null) {
    $now = Date::now();
    $datetime = new DateTime($datetime);
    $diff = $datetime->diff($now);
    return $diff->invert;
  }

  public static function isPast($datetime = null) {
    return !Date::isFuture($datetime);
  }

  public static function now($format = null) {
    $now = new DateTime('now');
    return is_null($format) ? $now : $now->format($format);
  }

  public static function format($datetime, $format = 'Y-m-d H:i:s') {
    if(!$datetime instanceof DateTime) {
      $timestamp = strtotime($datetime);
      $datetime = new DateTime();
      $datetime->setTimestamp($timestamp);
    }
    return $datetime->format($format);
  }

  public static function greeting() {

    // Morning midnight to noon
    // Afternoon noon to 5pm
    // Evening 5pm to midnight

    $time = Date::now('H'); // time starts from 00 to 23
    if($time >= 00 && $time < 12) {
      return "Good morning,";
    } else if($time >= 12 & $time < 17) {
      return "Good afternoon,";
    } else {
      return "Good evening,";
    }
  }

  /**
   * Get days between two dates with year and month
   */
  public static function getDatesFromRange($start, $end, $format = 'Ymd') {
    $interval = new DateInterval('P1D');

    $realStart = new DateTime($start);
    $realStart->setTime(0, 0);

    $realEnd = new DateTime($end);
    $realEnd->setTime(0, 0);
    $realEnd->add($interval);

    $period = new DatePeriod($realStart, $interval, $realEnd);

    $array = [];
    foreach($period as $date) {
      $array[] = $date->format($format);
    }
    return $array;
  }

  public static function ago($timestamp) {

    if(!is_numeric($timestamp)) {
      if($timestamp instanceof DateTime) {
        $timestamp = $timestamp->getTimestamp();
      } else {
        $timestamp = strtotime($timestamp);
      }
    }

    $now = Date::now()->getTimestamp();
    $diff = $now - $timestamp;

    if($diff >= Date::$seconds['year']) {
      return Date::ago_str($diff, Date::$seconds['year'], 'year');
    } elseif($diff >= Date::$seconds['month']) {
      return Date::ago_str($diff, Date::$seconds['month'], 'month');
    } elseif($diff >= Date::$seconds['week']) {
      return Date::ago_str($diff, Date::$seconds['week'], 'week');
    } elseif($diff >= Date::$seconds['two_days']) {
      return Date::ago_str($diff, Date::$seconds['day'], 'day');
    } elseif($diff >= Date::$seconds['day']) {
      return "Yesterday";
    } elseif($diff >= Date::$seconds['hour']) {
      return Date::ago_str($diff, Date::$seconds['hour'], 'hour');
    } elseif($diff >= Date::$seconds['half_hour']) {
      return "Half an hour ago";
    } elseif($diff >= Date::$seconds['minute']) {
      return Date::ago_str($diff, Date::$seconds['minute'], 'minute');
    } else {
      return "Just now";
    }

  }

  private static function ago_str($diff, $divisor, $unit) {
    (int) $units = floor($diff / $divisor);

    if($units == 0) {
      return "Less than 1 {$unit} ago";
    } elseif($units == 1) {
      return "1 {$unit} ago";
    } else {
      return "{$units} {$unit}s ago";
    }
  }

}
