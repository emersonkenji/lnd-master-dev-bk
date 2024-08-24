import React, { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";

const ButtonGroup = ({ renderButtons, onSelected, onSelectedActive }) => {
  // const [activeType, setActiveType] = useState(null);

  // // Atualiza o estado ativo quando a propriedade onSelectedActive muda
  // useEffect(() => {
  //   setActiveType(onSelectedActive);
  // }, [onSelectedActive]);
  const handleButtonClick = (button, index) => {
    // console.log(button.type);
    // setActiveType(button.type);
    onSelected(button.type);
  };

  return (
    <div className="gap-1 multi-button">
      {renderButtons.map((button, index) => (
        <Button
          key={index}
          className={`btn__group ${onSelectedActive === button.type ? "active" : ""}`}
          onClick={() => handleButtonClick(button, index)}
          size={"default"}
          variant={"outline"}
        >
          {button.label !== "" && (
            <>
              <span className="mr-2 btn__text">{button.label}</span>
            </>
          )}
          {button.icon !== "" && (
            <>
            {button.icon}
            </>
            // <button.icon className="w-4 h-4" />
            // <span className="btn__icon">
              // <i className={button.icon}></i>
            // </span>
          )}
        </Button>
      ))}
    </div>
  );
};

export default ButtonGroup;
