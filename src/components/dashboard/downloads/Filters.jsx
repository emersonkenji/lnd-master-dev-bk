import React from "react";
import ButtonGroup from "@/components/dashboard/downloads/ButtonGroup";
import ButtonClear from "@/components/dashboard/downloads/ButtonClear";
import InputSearch from "@/components/dashboard/downloads/InputSearch";
import { 
  LayoutGrid, 
  Plug2, 
  Palette,
  CalendarCheck2,
  FolderPen,
  ArrowDownAZ,
  ArrowUpAZ,
  SquarePercent
} from 'lucide-react';


const Filters = ({ 
  onSelectFilters, 
  onSelectType, 
  onSelectOrder, 
  onSelectOrderBy, 
  handleClearFilters,
  SelectedActiveFilters,
  SelectedActiveType,
  SelectedActiveOrder,
  SelectedActiveOrderBy,
  selectedValueSearch

}) => {
  // console.log(selectedValueSearch);
  
  const ButtonsFilters = [
    { icon: <LayoutGrid className="w-4 h-4" />, label: "Todos", type: "all", isActive: true },
    { icon: <SquarePercent className="w-4 h-4" />, label: "Grátis", type: "free", isActive: false, },
  ];

  const ButtonsType = [
    { icon: <Plug2 className="w-4 h-4" />, label: "Plugins", type: "plugin", isActive: false },
    { icon: <Palette className="w-4 h-4" />, label: "Temas", type: "theme", isActive: false },
  ];

  const ButtonsOrder = [
    { icon: <CalendarCheck2 className="w-4 h-4" />, label: "", type: "update_date", isActive: true },
    { icon: <FolderPen className="w-4 h-4" />, label: "", type: "item_name", isActive: false },
  ];

  const ButtonsOrderBy= [
    { icon: <ArrowDownAZ className="w-4 h-4" />, label: "", type: "desc", isActive: true },
    { icon: <ArrowUpAZ className="w-4 h-4" />, label: "", type: "asc", isActive: false },
  ];
  
  return (
    <div className="flex justify-between pt-2">
      <div className="content-filters-left">
        <ButtonGroup renderButtons={ButtonsFilters} onSelected={onSelectFilters} onSelectedActive={SelectedActiveFilters}/>
        <ButtonGroup renderButtons={ButtonsType} onSelected={onSelectType} onSelectedActive={SelectedActiveType}/>
        <ButtonGroup renderButtons={ButtonsOrder} onSelected={onSelectOrder} onSelectedActive={SelectedActiveOrder}/>
        <ButtonGroup renderButtons={ButtonsOrderBy} onSelected={onSelectOrderBy} onSelectedActive={SelectedActiveOrderBy}/>
        <ButtonClear handleButtonClear={handleClearFilters}/>

      </div>
      <div className="content-filters-right">
        <InputSearch onSubmitSearch={selectedValueSearch}/>
      </div>
    </div>
  );
};

export default Filters;