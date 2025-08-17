<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
	private const DEFAULT_TZ = 'UTC';

	/** (opsional) deteksi ISO 8601, termasuk .sssZ */
	public static function isIso8601Z(string $v): bool
	{
		return (bool) preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z$/i', $v);
		// Kalau ingin dukung offset ±hh:mm: gunakan: '/^\d{4}-\d{2}-\d{2}T.*(?:Z|[+\-]\d{2}:\d{2})$/i'
	}

	/** Normalisasi apapun (ISO/offset/ymd his) -> 'Y-m-d H:i:s' UTC untuk disimpan ke DB */
	public static function toDatabaseUTC($datetime): ?string
	{
		if (empty($datetime)) return null;
		return Carbon::parse($datetime)->timezone(self::DEFAULT_TZ)->format('Y-m-d H:i:s');
	}

	/**
	 * Konversi format tanggal ke format Indonesia.
	 */
	public static function formatTanggalIndonesia($dateTime, $formatType = 1)
	{
		if (!$dateTime) {
			return '-';
		}

		// Parse lalu tampilkan sebagai zona UTC
		$carbonDate = Carbon::parse($dateTime)->setTimezone(self::DEFAULT_TZ)->locale('id');

		// Mapping nama hari dan bulan dalam bahasa Indonesia
		$hari = $carbonDate->translatedFormat('l');
		$tanggal = $carbonDate->translatedFormat('j');
		$bulan = $carbonDate->translatedFormat('F');
		$tahun = $carbonDate->translatedFormat('Y');
		$jamMenit = $carbonDate->translatedFormat('H:i');

		switch ($formatType) {
			case 1: // Senin, 1 Januari 2025
				return "$hari, $tanggal $bulan $tahun";

			case 2: // Senin, 1 Januari 2025 pukul 15:30 WIB
				return "$hari, $tanggal $bulan $tahun pukul $jamMenit WIB";

			case 3: // 1 Januari 2025
				return "$tanggal $bulan $tahun";

			case 4: // 01-01-2025
				return $carbonDate->translatedFormat('d-m-Y');

			case 5: // 2025-01-01 15:30:00
				return $carbonDate->translatedFormat('Y-m-d H:i:s');

			case 6: // 01/01/2025
				return $carbonDate->translatedFormat('d/m/Y');

			default:
				return "$tanggal $bulan $tahun";
		}
	}

	// Tetap disediakan utilitas berikut kalau perlu
	public static function toUTC($datetime)
	{
		return self::parseDatetime($datetime)->setTimezone('UTC');
	}

	/**
	 * Pastikan datetime bisa diubah ke format yang benar
	 */
	private static function parseDatetime($datetime, $timezone = null): Carbon
	{
		if ($datetime instanceof Carbon) return $datetime;
		return Carbon::parse($datetime, $timezone);
	}
}
