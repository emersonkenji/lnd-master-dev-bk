import React, { useEffect, useState, useContext } from "react";
import { AppContext  } from "@/lib/context";
import { Outlet } from "react-router-dom";
import Header from '@/components/dashboard/header'; 
import Footer from "@/components/dashboard/Footer";

function Layout() {
  const { state, updateState } = useContext(AppContext);
  const [isSidebarClose, setIsSidebarClose] = useState(false);
  const [activePage, setActivePage] = useState("Dashboard");
  const [activeUser, setActiveUser] = useState("");
  return (
    <div className="flex flex-col min-h-screen ">
      <div className="flex flex-col w-full">
        {/* <Header dataUser={activeUser}/> */}
        <Header />
        {/* <Info /> */}
        <Outlet />
        {/* <Outlet context={{ dataUser: activeUser }}/> */}
        <Footer />
      </div>
    </div>
  );
}

export default Layout;
