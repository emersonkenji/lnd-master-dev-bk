import React, { useContext, useState } from "react";
import Pagination from "@/components/dashboard/Pagination";
import Cards from "@/components/dashboard/downloads/Cards";
import QuantityFilter from "@/components/dashboard/downloads/QuantityFilter";
import TotalFilters from "@/components/dashboard/downloads/TotalFilters";
import Filters from "@/components/dashboard/downloads/Filters";
import FiltersTop from "@/components/dashboard/downloads/FiltersTop";
import Alert from "@/components/dashboard/downloads/AlertUser";

import { AppContext } from "@/lib/context";

const Downloads = () => {
  const { state, updateState} = useContext(AppContext);
  const [membershipPlans, setMembershipPlans] = useState([]);
  const [subscriptionPlans, setSubscriptionPlans] = useState([]);

  const handleCurrentPage = ({ selected }) => {
    const newPage = selected + 1;
    if (newPage !== state.currentPage) {
      updateState({ currentPage: newPage });
    }
  };

  const handleQuantitySelect = (quantities) => {

    updateState({ perPage: quantities });
  };
  const handleSelectedFilters = (value) => {

    updateState({ activeFilter: value })
  };
  const handleSelectFiltersPlans = (value) => {

    updateState({ activePlans: value })
  };
  const handleSelectedType = (value) => {

    updateState({ activeType: value })
  };
  const handleSelectedOrder = (value) => {

    updateState({ activeOrder: value })
  };
  const handleSelectedOrderBy = (value) => {

    updateState({ activeOrderBy: value })
  };
  const handleSelectedcategory = (value) => {

    updateState({ activeCategory: value })
  };
  const handleSelectedSearch = (value) => {
   
    updateState({ searchText: value })
  };
  const handleClearFilters = () => {

    updateState({ currentPage: 1 });

    updateState({ perPage: 30 });

    updateState({ activeType: '' });

    updateState({ activeFilter: 'all' });

    updateState({ activeOrder: 'update_date' });
;
    updateState({ activeOrderBy: 'desc' });

    updateState({ searchText: '' });

    updateState({ activeCategory: '' });

    updateState({ activePlans: '' });
  };
  return (
    <section>
      <div className="px-6 mx-auto container-fluid">
        {!state.isLoading && <Alert />}
   
        <FiltersTop
          onSelectFiltersPlans={handleSelectFiltersPlans}
          SelectedActiveFiltersPlans={state.activePlans}
          category={state.category}
          onSelectedCategory={handleSelectedcategory}
          activeCategory={state.activeCategory}
        />
        <Filters
          onSelectFilters={handleSelectedFilters}
          onSelectType={handleSelectedType}
          onSelectOrder={handleSelectedOrder}
          onSelectOrderBy={handleSelectedOrderBy}
          handleClearFilters={handleClearFilters}
          SelectedActiveFilters={state.activeFilter}
          SelectedActiveType={state.activeType}
          SelectedActiveOrder={state.activeOrder}
          SelectedActiveOrderBy={state.activeOrderBy}
          selectedValueSearch={handleSelectedSearch}
        />
        <div className="content-filters">
          <div className="content-filters-left">
            <TotalFilters
              currentPage={state.currentPage}
              totalCards={state.totalCards}
              totalCardsPage={state.perPage}
              searchValue={state.searchText}
              loading={state.loading}
            />
          </div>
          <div className="content-filters-right">
            <QuantityFilter
              quantities={[30, 50, 80, 120]}
              onSelect={handleQuantitySelect}
            />
            <Pagination
              pageCount={state.totalPages}
              currentPage={state.currentPage}
              onPageChange={handleCurrentPage}
              loading={state.loading}
            />
          </div>
        </div>
        <Cards itensCards={state.cards} loading={state.loading} enableButtons={state.enableButtons} userPlans={state.userPlan}/> 
        <div className="content-filters">
          <div className="content-filters-left">
            <TotalFilters
              currentPage={state.currentPage}
              totalCards={state.totalCards}
              totalCardsPage={state.perPage}
              searchValue={state.searchText}
            />
          </div>
          <div className="content-filters-right">
            <QuantityFilter
              quantities={[30, 50, 80, 120]}
              onSelect={handleQuantitySelect}
            />
            <Pagination
              pageCount={state.totalPages}
              currentPage={state.currentPage}
              onPageChange={handleCurrentPage}
            />
          </div>
        </div>
      </div>
    </section>
  );
};

export default Downloads;
