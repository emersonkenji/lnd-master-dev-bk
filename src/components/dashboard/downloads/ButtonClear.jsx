import React from 'react';
import { Button } from "@/components/ui/button";
import { Eraser } from "lucide-react"
const ButtonClear = ({handleButtonClear}) => {

  return (
    <div className="multi-button">
      <Button
        className=" bg-destructive"
        variant="destructive"
        onClick={handleButtonClear}
      >
        <Eraser className="h-4 w-4" />
      </Button>
    </div>
  );
}

export default ButtonClear