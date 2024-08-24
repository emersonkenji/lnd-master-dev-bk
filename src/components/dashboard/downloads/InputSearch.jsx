import React, { useState } from 'react';
import { Search } from "lucide-react";

const InputSearch = ({ onSubmitSearch }) => {
  const [searchText, setSearchText] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    // Chama a função de envio fornecida pelo componente pai
    onSubmitSearch(searchText);
  };

  const handleChange = (e) => {
    setSearchText(e.target.value);
  };

  return (
    <form
      onSubmit={handleSubmit}
      role="search"
      className="ml-auto flex-1 sm:flex-initial"
    >
      <div className="relative">
        <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
        <input
          id="card-search"
          className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 pl-8 sm:w-[300px] md:w-[200px] lg:w-[300px]"
          type="search"
          placeholder="Search..."
          autoFocus
          required
          value={searchText}
          onChange={handleChange}
        />

      </div>
    </form>
  );
};

export default InputSearch;
