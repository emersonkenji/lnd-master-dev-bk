import React from "react";
import Sidebar from "@/components/dashboard/dash/Sidebar"
const dashboard = () => {

  return (
    <main className="flex flex-col flex-1 gap-4 position-relative md:gap-8 ">
      <Sidebar />
    </main>
  );
};

export default dashboard;
