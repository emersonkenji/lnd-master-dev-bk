import React from "react";
import { CalendarCheck, CaseUpper } from "lucide-react";



const FilterOrder = ({ activeFilter, onFilterChange }) => {
  return (
    <div className="filter-buttons-two">
      <button
        className={`filter-button ${
          activeFilter === "update_date" ? "active" : ""
        }`}
        onClick={() => onFilterChange("update_date")}
      >
        <CalendarCheck size={20} strokeWidth={1.75} absoluteStrokeWidth />

        Data de atualização
      </button>
      <button
        className={`filter-button ${
          activeFilter === "item_name" ? "active" : ""
        }`}
        onClick={() => onFilterChange("item_name")}
      >
        <CaseUpper size={20} strokeWidth={1.75} absoluteStrokeWidth />
        Nome do item
      </button>
    </div>
    
  );
};

export default FilterOrder;
