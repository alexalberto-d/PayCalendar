<?php

namespace App\Models;

use App\Core\Database;
use DateTime;

class Subscription
{
    public static function all()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM subscriptions WHERE active = 1 ORDER BY next_renewal ASC");
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM subscriptions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();

        $next_renewal = self::calculateNextRenewal($data['start_date'], $data['billing_cycle']);

        $sql = "INSERT INTO subscriptions (name, price, currency, billing_cycle, start_date, next_renewal, category, color) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['price'],
            $data['currency'] ?? 'USD',
            $data['billing_cycle'],
            $data['start_date'],
            $next_renewal,
            $data['category'],
            $data['color']
        ]);

        return $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance()->getConnection();

        // If start_date or billing_cycle changed, recalculate next_renewal
        // For simplicity in this version, we always recalculate based on the provided start_date
        // or just let the user provide next_renewal? No, better calculate it.
        $next_renewal = self::calculateNextRenewal($data['start_date'], $data['billing_cycle']);

        $sql = "UPDATE subscriptions SET 
                name = ?, price = ?, currency = ?, billing_cycle = ?, 
                start_date = ?, next_renewal = ?, category = ?, color = ?
                WHERE id = ?";
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
            $id
        ]);
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM subscriptions WHERE id = ?");
        $stmt->execute([$id]);
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
