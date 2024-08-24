import React from "react";
import { createHashRouter } from "react-router-dom";
import ErrorPage from "@/pages/NotFoundPage";
import Layout from "@/content/layout";
import Dashboard from "@/content/dashboard";
import Downloads from "@/content/downloads";
import Templates from "@/content/templates";
import Info from "@/content/info";
import Solution from "@/content/solution";
import PricingSection from "@/content/pricingSection";
import Plans from "@/components/dashboard/dash/plans";

const router = createHashRouter([
  {
    path: "/",
    element: <Layout />,
    // errorElement: <ErrorPage />,
    children: [
      {
        path: "/",
        element: <Dashboard />,
        // errorElement: <ErrorPage />,
        children: [
          {
            path: "/dash",
            element: <Plans />,
          },
        ],
      },
      {
        path: "/downloads",
        element: <Downloads />,
        // errorElement: <ErrorPage />,
      },
      {
        path: "/templates",
        element: <Templates />,
        // errorElement: <ErrorPage />,
      },
      {
        path: "/prices",
        element: <PricingSection />,
        // errorElement: <ErrorPage />,
      },
      {
        path: "/info",
        element: <Info />,
        // errorElement: <ErrorPage />,
      },
      {
        path: "/solution",
        element: <Solution />,
        // errorElement: <ErrorPage />,
      },
    ],
  },
]);

export default router;
