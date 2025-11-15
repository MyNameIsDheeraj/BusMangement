import React from 'react';

export default function Tooltip({ children, text }) {
  return (
    <div className="relative inline-block group">
      {children}
      <div className="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 whitespace-nowrap bg-gray-900 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity">
        {text}
      </div>
    </div>
  );
}
