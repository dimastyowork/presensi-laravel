<?php

namespace App\Helpers;

class NameHelper
{
    /**
     * Daftar gelar yang umum digunakan di Indonesia
     */
    private static $titles = [
        'dr.', 'dr', 'ns.', 'ns', 'apt.', 'apt', 'amd.', 'amd',
        'prof.', 'prof', 'ir.', 'ir', 'drs.', 'drs', 'drg.', 'drg',
        's.kom', 's.kom.', 's.pd', 's.pd.', 's.si', 's.si.', 's.t', 's.t.',
        's.sos', 's.sos.', 's.e', 's.e.', 's.h', 's.h.', 's.kep', 's.kep.',
        's.farm', 's.farm.', 's.psi', 's.psi.', 's.ked', 's.ked.',
        'm.kom', 'm.kom.', 'm.pd', 'm.pd.', 'm.si', 'm.si.', 'm.t', 'm.t.',
        'm.m', 'm.m.', 'm.h', 'm.h.', 'm.kes', 'm.kes.',
    ];

    /**
     * Mengambil nama pertama yang bukan gelar
     * 
     * @param string $fullName
     * @return string
     */
    public static function getFirstName($fullName)
    {
        if (empty($fullName)) {
            return '';
        }

        // Pisahkan nama berdasarkan spasi
        $nameParts = explode(' ', trim($fullName));
        
        // Cari kata pertama yang bukan gelar
        foreach ($nameParts as $part) {
            $cleanPart = trim($part);
            $lowerPart = strtolower(str_replace('.', '', $cleanPart));
            
            // Jika bukan gelar dan tidak kosong, return
            if (!empty($cleanPart) && !in_array($lowerPart, self::$titles) && !in_array(strtolower($cleanPart), self::$titles)) {
                return $cleanPart;
            }
        }
        
        // Jika semua kata adalah gelar (edge case), return kata pertama
        return $nameParts[0] ?? '';
    }

    /**
     * Mengambil nama lengkap tanpa gelar
     * 
     * @param string $fullName
     * @return string
     */
    public static function getNameWithoutTitle($fullName)
    {
        if (empty($fullName)) {
            return '';
        }

        $nameParts = explode(' ', trim($fullName));
        $cleanParts = [];
        
        foreach ($nameParts as $part) {
            $cleanPart = trim($part);
            $lowerPart = strtolower(str_replace('.', '', $cleanPart));
            
            // Tambahkan hanya jika bukan gelar
            if (!empty($cleanPart) && !in_array($lowerPart, self::$titles) && !in_array(strtolower($cleanPart), self::$titles)) {
                $cleanParts[] = $cleanPart;
            }
        }
        
        return implode(' ', $cleanParts) ?: ($nameParts[0] ?? '');
    }
}
