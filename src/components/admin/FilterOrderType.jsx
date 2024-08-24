import React from "react";
import { Palette, Plug } from "lucide-react"




const FilterOrderType = ({ activeFilter, onFilterChange }) => {
  return (
    <div className="filter-buttons">
      <button
        className={`filter-button ${activeFilter === "plugin" ? "active" : ""}`}
        onClick={() => onFilterChange("plugin")}
      >
        <Plug size={20} strokeWidth={1.75} absoluteStrokeWidth />
        Plugin
      </button>
      <button
        className={`filter-button ${activeFilter === "theme" ? "active" : ""}`}
        onClick={() => onFilterChange("theme")}
      >
        <Palette  size={20} strokeWidth={1.75} absoluteStrokeWidth />
        Tema
      </button>
    </div>
  );
};

export default FilterOrderType;
