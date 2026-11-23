-- =====================================================
-- 1. REKAP JAM PEMAKAIAN RUANGAN
-- =====================================================

SELECT
    r.id AS room_id,
    r.name,
    COALESCE(SUM(EXTRACT(HOUR FROM (b.end_time - b.start_time)))::integer, 0) AS total_durasi_jam
FROM rooms r
LEFT JOIN bookings b
    ON b.room_id = r.id
    AND b.status = 'CONFIRMED'
    AND b.deleted_at IS NULL
WHERE r.deleted_at IS NULL
GROUP BY r.id, r.name
ORDER BY r.id;


-- =====================================================
-- 2. PENCARIAN RUANGAN TERSEDIA
-- Periode: 01 Desember 2026, pukul 10:00 - 12:00
-- =====================================================

SELECT
    r.id AS room_id,
    r.name,
    r.capacity
FROM rooms r
WHERE r.is_active = true
    AND r.deleted_at IS NULL
    AND NOT EXISTS (
        SELECT 1
        FROM bookings b
        WHERE b.room_id = r.id
            AND b.status = 'CONFIRMED'
            AND b.deleted_at IS NULL
            AND b.start_time < '2026-12-01 12:00:00'
            AND b.end_time > '2026-12-01 10:00:00'
    )
ORDER BY r.id;