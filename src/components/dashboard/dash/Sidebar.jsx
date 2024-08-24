import React, { memo, useContext, useMemo } from "react";
import { NavLink } from "react-router-dom";
import { X } from "lucide-react";
import CardUser from "./cardUser";
import { AppContext } from "@/lib/context";
import Plans from "./plans";

const NavItem = memo(({ to, icon, label }) => (
  <li>
    <div className="border rounded shadow bg-card text-card-foreground">
      <NavLink
        to={to}
        className={({ isActive }) =>
          `flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group transition-colors hover:text-foreground ${
            isActive
              ? "text-foreground font-bold"
              : "text-muted-foreground"
          }`
        }
      >
        {icon}
        <span className="ms-3">{label}</span>
      </NavLink>
    </div>
  </li>
));

const BetaAlert = memo(() => (
  <div
    id="dropdown-cta"
    className="p-4 mt-6 rounded-lg bg-blue-50 dark:bg-blue-900"
    role="alert"
  >
    <div className="flex items-center mb-3">
      <span className="bg-orange-100 text-orange-800 text-sm font-semibold me-2 px-2.5 py-0.5 rounded dark:bg-orange-200 dark:text-orange-900">
        Beta
      </span>
      <button
        type="button"
        className="ms-auto -mx-1.5 -my-1.5 bg-blue-50 inline-flex justify-center items-center w-6 h-6 text-blue-900 rounded-lg focus:ring-2 focus:ring-blue-400 p-1 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400 dark:hover:bg-blue-800"
        data-dismiss-target="#dropdown-cta"
        aria-label="Close"
      >
        <span className="sr-only">Close</span>
        <X />
      </button>
    </div>
    
  </div>
));

const Sidebar = memo(() => {
  const { state } = useContext(AppContext);
  // console.log(state);
  

  const dataUsers = useMemo(() => state.userData?.plans || false, [state.userData]);

  const dashboardIcon = (
    <svg
      className="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
      aria-hidden="true"
      xmlns="http://www.w3.org/2000/svg"
      fill="currentColor"
      viewBox="0 0 22 21"
    >
      <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
      <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
    </svg>
  );

  return (
    <aside
      id="cta-button-sidebar"
      className="grid grid-cols-12 gap-2"
      aria-label="Sidebar"
    >
      <div className="left-0 h-full min-h-[calc(100vh-4rem-4.4rem)] col-span-3 col-start-1 px-3 py-4 overflow-y-auto bg-card">
        <ul className="space-y-2 font-medium">
          <li>
            <CardUser />
          </li>
          {/* <NavItem to="/dash" icon={dashboardIcon} label="Dashboard" />
          <NavItem to="/" icon={dashboardIcon} label="Dashboard" /> */}
        </ul>
        <BetaAlert />
      </div>

      <div className="col-span-9 p-4 rounded-sm">
        <div className="grid grid-cols-1 gap-4 mb-4">
          <Plans data={dataUsers} user={state.userData} />
        </div>
      </div>
    </aside>
  );
});

export default Sidebar;