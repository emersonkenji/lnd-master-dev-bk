import React, { useState } from "react";
import ReactLoading from "react-loading";
import { toast } from 'react-toastify';
import { ArrowDownUp, ArrowUpDown, RefreshCw, Search, X } from "lucide-react";


// import 'react-toastify/dist/ReactToastify.css';
import "./Filters.scss";

const Filters = ({ onSortChange, onSubmit, onCatUpdate, onCatClear, updating}) => {
  const [sortType, setSortType] = useState("desc"); // Estado para controlar a ordenação
  // const [updating, setUpdating] = useState(false);
  // const [searchValue, setSearchValue] = useState("");
  // const notify = () => {
  //   // toast("Mensagem de notificação aqui");
  //   toast.success('🦄 Wow so easy!');
  // };
  

  const handleSortChange = () => {
    // Alternar a ordenação entre "asc" e "desc"
    const newSortType = sortType === "asc" ? "desc" : "asc";
    setSortType(newSortType);
    onSortChange(newSortType);
  };

  const handleSearchSubmit = (event) => {
    event.preventDefault();
    // setSearchValue(event.target[0].value);
    onSubmit(event); // Chama a prop onSubmit passando o evento
    
  };

  const handleonCatUpdate = (event) => {
    event.preventDefault();
    // setSearchValue(event.target[0].value);
    onCatUpdate(event, updating); // Chama a prop onSubmit passando o evento
    console.log( updating);
  };

  return (
    <div className="multi-button">
      <div className="filterForm">
        {/* <form onSubmit={(event) => event.preventDefault()} role="search"> */}
        <form onSubmit={handleSearchSubmit} role="search">
          <input
            id="search"
            type="search"
            placeholder="Pesquisar..."
            autoFocus
            required
          />
          <button type="submit" className="">
            <Search size={20} strokeWidth={1.75} absoluteStrokeWidth /> 
          </button>
        </form>
      </div>

      <div className="filterButtons">
        <button onClick={onCatClear} className="button-clear">
        <X size={20} strokeWidth={1.75} absoluteStrokeWidth />
        </button>

        <button onClick={handleonCatUpdate} disabled={updating} className="button-sort-type">
          {updating ? (
            <ReactLoading type={"bars"} color={"#fff"} height={"30px"} width={"30px"} />
          ) : (
            <RefreshCw size={20} strokeWidth={1.75} absoluteStrokeWidth />
          )}
        </button>

        <button onClick={handleSortChange} className="button-sort-type">
          {sortType === "asc" ? (
            <ArrowDownUp size={20} strokeWidth={1.75} absoluteStrokeWidth />
          ) : (
            <ArrowUpDown size={20} strokeWidth={1.75} absoluteStrokeWidth />
          )}
        </button>
      </div>
    </div>
  );
};

export default Filters;