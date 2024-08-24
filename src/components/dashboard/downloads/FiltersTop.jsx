import React from 'react';
import ButtonGroup from "@/components/dashboard/downloads/ButtonGroup";
import FilterSelect from '@/components/dashboard/downloads/FilterSelect';
import {ArrowLeft} from "lucide-react"
const FiltersTop = ({ 
    onSelectFiltersPlans, 
    SelectedActiveFiltersPlans,
    category,
    onSelectedCategory,
    activeCategory
  }) => {
    // const ButtonsPlans = [
    //   { icon: "", label: "Basico", type: "basic" },
    //   { icon: "", label: "Gold", type: "gold" },
    //   { icon: "", label: "Profissonal", type: "profissional" },
    //   { icon: "", label: "Diamante", type: "diamond" },
    //   { icon: "", label: "Completo", type: "" },
    // ];
    const ButtonsPlans = [
      { icon: "", label: "Basico", type: "1" },
      { icon: "", label: "Gold", type: "2" },
      { icon: "", label: "Profissonal", type: "3" },
      { icon: "", label: "Diamante", type: "4" },
      { icon: "", label: "Completo", type: "5" },
    ];
    
    return (
      <div className="flex justify-between pt-2">
        <div className="content-filters-left">
          <ButtonGroup renderButtons={ButtonsPlans} onSelected={onSelectFiltersPlans} onSelectedActive={SelectedActiveFiltersPlans}/>  
          <p className='flex gap-2 text-sm '><ArrowLeft className="w-4 h-4 animate-pulse stroke-orange-500"/> <span>Clique no seu plano e veja seus downloads</span></p>
        </div>
        <div className="content-filters-right">
        <FilterSelect renderOptions={category} onSelectedCategory={onSelectedCategory} activeCategory={activeCategory}/>
        </div>
      </div>
    );
  };

export default FiltersTop