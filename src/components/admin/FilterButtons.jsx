import React from "react";
import {  Check, LayoutGrid, House} from "lucide-react"


const FilterButtons = ({ activeFilter, onFilterChange }) => {
  return (
    <div className="filter-buttons">
      <button
        className={`filter-button ${activeFilter === "all" ? "active" : ""}`}
        onClick={() => onFilterChange("all")}
      >
        <LayoutGrid size={20} strokeWidth={1.75} absoluteStrokeWidth />
        Todos
      </button>
      <button
        className={`filter-button ${activeFilter === "free" ? "active" : ""}`}
        onClick={() => onFilterChange("free")}
      >
        
        <House  size={20} strokeWidth={1.75} absoluteStrokeWidth />
        Grátis
      </button>
      <button
        className={`filter-button ${activeFilter === "installed" ? "active" : ""}`}
        onClick={() => onFilterChange("installed")}
      >
         <Check  size={20} strokeWidth={1.75} absoluteStrokeWidth/>
        Instalados
      </button>
    </div>
  );
};

export default FilterButtons;
