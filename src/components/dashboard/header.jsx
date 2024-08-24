import React from "react";
import { Link, NavLink } from "react-router-dom";
import { Download, Menu, Search } from "lucide-react";

import { Input } from "@/components/ui/input";

import { Button } from "@/components/ui/button";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";
import ModeToggler from "@/components/dashboard/ModeToggler";
import MenuDropdown from "@/components/dashboard/MenuDropdown";
import AuthModal from "../global/AuthModal";

const header = ({ dataUser }) => {

  return (
    <header className="sticky top-0 z-10 flex items-center h-16 gap-4 px-4 border-b bg-background md:px-6">
      <nav className="flex-col hidden gap-6 text-lg font-medium md:flex md:flex-row md:items-center md:gap-5 md:text-sm lg:gap-6">
        <NavLink
          to="/"
          className={({ isActive }) =>
            `flex items-center gap-2 text-lg font-semibold md:text-base ${
              isActive ? "text-foreground" : "text-muted-foreground"
            }`
          }
        > 
          <Download className="relative p-2 rounded stroke-current h-14 w-14 top-3 bg-background" />

          <span className="sr-only">Acme Inc</span>
        </NavLink>
        <NavLink
          to="/"
          className={({ isActive }) =>
            `transition-colors hover:text-foreground ${
              isActive ? "text-foreground font-bold" : "text-muted-foreground"
            }`
          }
        >
          Dashboard
        </NavLink>
        <NavLink
          to="/downloads"
          className={({ isActive }) =>
            `transition-colors hover:text-foreground ${
              isActive ? "text-foreground font-bold" : "text-muted-foreground"
            }`
          }
        >
          Downloads
        </NavLink>
        <NavLink
          to="/templates"
          className={({ isActive }) =>
            `transition-colors hover:text-foreground ${
              isActive ? "text-foreground font-bold" : "text-muted-foreground"
            }`
          }
        >
          Templates
        </NavLink>
        <NavLink
          to="/prices"
          className={({ isActive }) =>
            `transition-colors hover:text-foreground ${
              isActive ? "text-foreground font-bold" : "text-muted-foreground"
            }`
          }
        >
          Preços
        </NavLink>
        <NavLink
          to="/info"
          className={({ isActive }) =>
            `transition-colors hover:text-foreground ${
              isActive ? "text-foreground font-bold" : "text-muted-foreground"
            }`
          }
        >
          Informações
        </NavLink>
        <NavLink
          to="/solution"
          className={({ isActive }) =>
            `transition-colors hover:text-foreground ${
              isActive ? "text-foreground font-bold" : "text-muted-foreground"
            }`
          }
        >
          Soluções
        </NavLink>
      </nav>
      <Sheet>
        <SheetTrigger asChild>
          <Button variant="outline" size="icon" className="shrink-0 md:hidden">
            <Menu className="w-5 h-5" />
            <span className="sr-only">Toggle navigation menu</span>
          </Button>
        </SheetTrigger>
        <SheetContent side="left">
          <nav className="grid gap-6 text-lg font-medium">
            <Link
              href="#"
              className="flex items-center gap-2 text-lg font-semibold"
            >
              <Download className="w-12 h-12" />
              <span className="sr-only">Acme Inc</span>
            </Link>
            <Link href="#" className="hover:text-foreground">
              Dashboard
            </Link>
            <Link
              href="#"
              className="text-muted-foreground hover:text-foreground"
            >
              Orders
            </Link>
            <Link
              href="#"
              className="text-muted-foreground hover:text-foreground"
            >
              Products
            </Link>
            <Link
              href="#"
              className="text-muted-foreground hover:text-foreground"
            >
              Customers
            </Link>
            <Link
              href="#"
              className="text-muted-foreground hover:text-foreground"
            >
              Analytics
            </Link>
          </nav>
        </SheetContent>
      </Sheet>
      <div className="flex items-center w-full gap-4 md:ml-auto md:gap-2 lg:gap-4">
        <form className="flex-1 ml-auto sm:flex-initial">
          <div className="relative">
            <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input
              type="search"
              placeholder="Search products..."
              className="pl-8 sm:w-[300px] md:w-[200px] lg:w-[300px]"
            />
          </div>
        </form>
        <ModeToggler />
        <MenuDropdown />
        <AuthModal />
        {/* <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button variant="secondary" size="icon" className="">
              <CircleUser className="w-5 h-5" />
              <span className="sr-only">Toggle user menu</span>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuLabel>My Account</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem>Settings</DropdownMenuItem>
            <DropdownMenuItem>Support</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem>Logout</DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu> */}
      </div>
    </header>
  );
};

export default header;
