export default function PaginatedTable({ children }) {
  // Placeholder wrapper for a paginated table
  return (
    <div className="bg-white rounded shadow p-2">
      {children}
    </div>
  );
}
