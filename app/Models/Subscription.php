<?php

namespace App\Models;

use App\Core\Database;
use DateTime;

class Subscription
{
    public static function all($userId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND active = 1 ORDER BY next_renewal ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function find($id, $userId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM subscriptions WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function create($userId, $data)
    {
        $db = Database::getInstance()->getConnection();

        $next_renewal = self::calculateNextRenewal($data['start_date'], $data['billing_cycle']);

        $sql = "INSERT INTO subscriptions (user_id, name, price, currency, billing_cycle, start_date, next_renewal, category, color, end_date, emoji) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $userId,
            $data['name'],
            $data['price'],
            $data['currency'] ?? 'USD',
            $data['billing_cycle'],
            $data['start_date'],
            $next_renewal,
            $data['category'],
            $data['color'],
            !empty($data['end_date']) ? $data['end_date'] : null,
            $data['emoji'] ?? ''
        ]);

        return $db->lastInsertId();
    }

    public static function update($id, $userId, $data)
    {
        $db = Database::getInstance()->getConnection();

        $next_renewal = self::calculateNextRenewal($data['start_date'], $data['billing_cycle']);

        $sql = "UPDATE subscriptions SET 
                name = ?, price = ?, currency = ?, billing_cycle = ?, 
                start_date = ?, next_renewal = ?, category = ?, color = ?, end_date = ?, emoji = ?
                WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['price'],
            $data['currency'],
            $data['billing_cycle'],
            $data['start_date'],
            $next_renewal,
            $data['category'],
            $data['color'],
            !empty($data['end_date']) ? $data['end_date'] : null,
            $data['emoji'] ?? '',
            $id,
            $userId
        ]);
    }

    public static function delete($id, $userId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM subscriptions WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }

    public static function calculateNextRenewal($startDate, $cycle)
    {
        $date = new DateTime($startDate);
        $now = new DateTime('today');

        // If the start date is today or in the future, that's a renewal (or the first one)
        if ($date >= $now) {
            return $date->format('Y-m-d');
        }

        // Calculate the next occurrence from today onwards
        while ($date < $now) {
            if ($cycle === 'weekly') {
                $date->modify('+1 week');
            } elseif ($cycle === 'biweekly') {
                $day = (int) $date->format('d');
                $monthDayCount = (int) $date->format('t');

                if ($day < 15) {
                    $date->setDate((int) $date->format('Y'), (int) $date->format('m'), 15);
                } else {
                    // Go to next month 1st then calc 15 or EOM? 
                    // Simpler: if it was 15, go to EOM. If it was EOM, go to next month 15.
                    if ($day >= 15 && $day < $monthDayCount) {
                        $date->setDate((int) $date->format('Y'), (int) $date->format('m'), $monthDayCount);
                    } else {
                        $date->modify('first day of next month');
                        $date->setDate((int) $date->format('Y'), (int) $date->format('m'), 15);
                    }
                }
            } elseif ($cycle === 'monthly') {
                $date->modify('+1 month');
            } elseif ($cycle === 'yearly') {
                $date->modify('+1 year');
            } else {
                break;
            }
        }

        return $date->format('Y-m-d');
    }
}
