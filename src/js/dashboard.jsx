import React from "react";
import { createRoot } from "react-dom/client";
import { RouterProvider } from "react-router-dom";
import router from "@/router/dashboard/router";
import { AppProvider } from "@/lib/context";
import ErrorBoundary from "@/lib/ErrorBoundary";
// import { __ } from "@wordpress/i18n";
// import "./sass/Dashboard.scss";

const containers = document.querySelectorAll(".page-dashboard");

containers.forEach((container) => {
  createRoot(container).render(
    <ErrorBoundary>
      <React.StrictMode>
        <AppProvider>
          <RouterProvider router={router} />
        </AppProvider>
      </React.StrictMode>
    </ErrorBoundary>
  );
});
