/* eslint-env node */
/* global process */
// Simple smoke tests that read service files and check for expected export names
import { readFile } from 'fs/promises';
let pass = true;

const check = (name, ok) => {
  if (!ok) {
    console.error(`FAIL: ${name} missing`);
    pass = false;
  } else {
    console.log(`OK: ${name}`);
  }
};

const run = async () => {
  const studentsSrc = await readFile(new URL('../src/services/students.js', import.meta.url), 'utf8');
  const attendanceSrc = await readFile(new URL('../src/services/attendance.js', import.meta.url), 'utf8');

  check('studentsService.getStudents', /getStudents\s*[:=]/.test(studentsSrc) || /getStudents\(/.test(studentsSrc));
  check('studentsService.createStudent', /createStudent\s*[:=]/.test(studentsSrc) || /createStudent\(/.test(studentsSrc));
  check('studentsService.updateStudent', /updateStudent\s*[:=]/.test(studentsSrc) || /updateStudent\(/.test(studentsSrc));

  check('attendanceService.getAttendances', /getAttendances\s*[:=]/.test(attendanceSrc) || /getAttendances\(/.test(attendanceSrc));
  check('attendanceService.markAttendance', /markAttendance\s*[:=]/.test(attendanceSrc) || /markAttendance\(/.test(attendanceSrc));

  if (!pass) process.exit(2);
  console.log('Smoke tests passed');
};

run().catch((err) => {
  console.error('Smoke tests error', err);
  process.exit(3);
});
