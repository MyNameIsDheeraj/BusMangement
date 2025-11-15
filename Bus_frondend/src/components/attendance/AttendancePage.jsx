import AttendanceForm from './AttendanceForm';
import { useAuth } from '../../contexts/AuthContext';
import { hasPermission } from '../../utils/helpers';
import { PERMISSIONS } from '../../utils/constants';

export default function AttendancePage() {
  const { user } = useAuth();

  if (!hasPermission(user, PERMISSIONS.MARK_ATTENDANCE) && !hasPermission(user, PERMISSIONS.VIEW_ATTENDANCE)) {
    return <div className="p-4">You do not have permission to view attendance.</div>;
  }

  return (
    <div className="p-4">
      <h2 className="text-xl font-semibold mb-4">Mark Attendance</h2>
      <AttendanceForm onSaved={() => { /* could show toast or refresh history */ }} />
    </div>
  );
}
