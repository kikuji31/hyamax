<?php
require_once __DIR__ . '/security.php';
deny_direct_access(__FILE__);
/*
 * บัญชีผู้ใช้ Hyamax Academy — หนึ่งบรรทัดต่อหนึ่งคน
 * อย่าแก้ salt/hash ด้วยมือ: เพิ่มคนด้วย tools/add-user.ps1 / ลบคนให้ลบบรรทัดนั้นทิ้ง
 * (ลบแล้วคนนั้นจะถูกออกจากระบบทันทีในการเปิดหน้าครั้งถัดไป)
 *
 * รหัสผ่านเก็บเป็น PBKDF2-SHA256 (100,000 รอบ) ไม่มีรหัสจริงอยู่ในไฟล์นี้
 */

const ACADEMY_PBKDF2_ITERATIONS = 100000;

const ACADEMY_USERS = [
    // 'username' => ['name' => 'ชื่อที่แสดง', 'salt' => '...', 'hash' => '...'],
    'doctor' => ['name' => 'Shared account', 'salt' => '6588f387997c07b7362a872412d37967', 'hash' => 'a144300f0d0358381d9b069423e51b5dfaf164fcc3224b0d0fca8191dba05fc5'],
];
