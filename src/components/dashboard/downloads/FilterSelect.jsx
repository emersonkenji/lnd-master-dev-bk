import React, { useEffect, useState } from "react";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

const FilterSelect = (props) => {
  const [activeCategory, setActiveCategory] = useState('');
  
  useEffect(() => {
    setActiveCategory(props.activeCategory); // Se props.
  }, [props.activeCategory]);

  const handleChange = (value) => {
    setActiveCategory(value);
    props.onSelectedCategory(value);
    console.log(value);
  };

  return (
    <Select value={activeCategory} onValueChange={handleChange}>
      <SelectTrigger className="w-[300px]">
        <SelectValue placeholder="Escolha a categoria" />
      </SelectTrigger>
      <SelectContent >
        <SelectGroup className="w-[200px]">
          <SelectLabel>Categorias</SelectLabel>
          {props.renderOptions.length > 1 &&
          props.renderOptions.map((option, index) => (
            <SelectItem key={index} value={option.id}>{option.name}</SelectItem>
          ))}
        </SelectGroup>
      </SelectContent>
    </Select>
  );
};

export default FilterSelect;
