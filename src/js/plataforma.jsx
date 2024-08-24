import React from "react";
import { createRoot } from 'react-dom/client';
import AdminMaster from '@/pages/adminMaster';

const containers = document.querySelectorAll(".react-plugin");

containers.forEach((container) => {
  if (container) { // Verifique se o container é válido
    const root = createRoot(container);
    root.render(<AdminMaster />);
  }
});

