import React, { useState } from "react";

const QuantityFilter = ({ quantities, onSelect }) => {
  const [activeIndex, setActiveIndex] = useState(null);

  const handleQuantitySelect = (index, quantity) => {
    setActiveIndex(index);
    onSelect(quantity);
  };

  return (
    <div className="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 gap-1 px-2">
      <span className="per-page-title"> Mostrar: </span>
      {quantities.map((quantity, index) => (
        <React.Fragment key={index}>
          <input
            type="button"
            className={`input-btn-quantity  cursor-pointer ${activeIndex === index ? 'active' : 'opacity-50'}`}
            value={quantity}
            onClick={() => handleQuantitySelect(index, quantity)}
          />
          {index !== quantities.length - 1 && (
            <span className="per-page-border">/</span>
          )}
        </React.Fragment>
      ))}
    </div>
  );
};

export default QuantityFilter;
