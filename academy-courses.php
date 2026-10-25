<?php
/*
 * ข้อมูลคอร์สทั้งหมดของ Hyamax Academy — แก้เนื้อหาที่ไฟล์นี้ไฟล์เดียว
 *
 * แต่ละคอร์ส:
 *   'title'  ชื่อคอร์สที่แสดงบนการ์ดและหัวหน้า
 *   'videos' รายการวิดีโอ (ถ้าไม่ใส่ หรือเป็น [] จะแสดงเป็น "Coming Soon")
 *
 * แต่ละวิดีโอ:
 *   'label'   ป้ายบนวิดีโอ เช่น Lecture / Live Demonstration
 *   'thumb'   รูปปก
 *   'src'     ลิงก์วิดีโอ: YouTube, Vimeo หรือไฟล์ .mp4 — เว้นว่าง '' = ยังไม่เปิดให้ดู
 *   'speaker' ชื่อวิทยากร (เว้นว่างได้ จะไม่แสดง)
 *   'date'    วันที่ เช่น '15 Nov 2026' (เว้นว่างได้ จะไม่แสดง)
 *
 * แนะนำ YouTube แบบ "Unlisted" หรือ Vimeo แบบจำกัดโดเมน — ไฟล์ .mp4 ที่วางบนเซิร์ฟเวอร์
 * เปิดได้ทุกคนที่รู้ลิงก์ แม้ไม่ได้ login
 */

const ACADEMY_COURSES = [
    'master-trainer' => [
        'title'  => 'Master Trainer',
        'videos' => [
            ['label' => 'Lecture', 'thumb' => 'academy-lecture-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
            ['label' => 'Live Demonstration', 'thumb' => 'academy-demo-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
        ],
    ],
    'train-the-trainer' => [
        'title'  => 'Train the Trainer',
        'videos' => [
            ['label' => 'Lecture', 'thumb' => 'academy-lecture-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
            ['label' => 'Live Demonstration', 'thumb' => 'academy-demo-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
        ],
    ],
    '1st-hands-on' => [
        'title'  => '1st Hands On',
        'videos' => [
            ['label' => 'Lecture', 'thumb' => 'academy-lecture-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
            ['label' => 'Live Demonstration', 'thumb' => 'academy-demo-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
        ],
    ],
    'webinar' => [
        'title'  => 'Webinar',
        'videos' => [
            ['label' => 'Lecture', 'thumb' => 'academy-lecture-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
            ['label' => 'Live Demonstration', 'thumb' => 'academy-demo-thumb.jpg', 'src' => '', 'speaker' => '', 'date' => ''],
        ],
    ],
    'coming-soon-1' => ['title' => 'Coming Soon', 'videos' => []],
    'coming-soon-2' => ['title' => 'Coming Soon', 'videos' => []],
    'coming-soon-3' => ['title' => 'Coming Soon', 'videos' => []],
    'coming-soon-4' => ['title' => 'Coming Soon', 'videos' => []],
];

/** แปลงลิงก์ YouTube/Vimeo เป็นลิงก์ embed; ไฟล์วิดีโอปกติคืนค่าเดิม */
function academy_embed(string $src): array
{
    if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{11})~', $src, $m)) {
        return ['type' => 'iframe', 'src' => 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0'];
    }
    if (preg_match('~vimeo\.com/(?:video/)?(\d+)(?:/(\w+))?~', $src, $m)) {
        $hash = isset($m[2]) ? '&h=' . $m[2] : '';
        return ['type' => 'iframe', 'src' => 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1' . $hash];
    }
    return ['type' => 'video', 'src' => $src];
}
