
import React from 'react';
import ReactDOM from 'react-dom/client';

// This file serves as a minimal React entry point
// Your PHP application will continue to function separately

ReactDOM.createRoot(document.getElementById('app')).render(
  <React.StrictMode>
    <div className="text-center mt-4 text-gray-500 text-sm">
      <p>React is initialized, but your PHP application will continue to operate through its standard URLs.</p>
    </div>
  </React.StrictMode>
);
