
import React from 'react'
import TotalFiltersLoading from '@/components/dashboard/downloads/TotalFiltersLoading';

const TotalFilters = ({currentPage, totalCards, totalCardsPage, searchValue, loading}) => {
  if(loading) {
    return(
      <TotalFiltersLoading />
    )
  }
  return (
    <div className="inline-flex items-center justify-center gap-1 px-2 text-sm font-medium transition-colors rounded-md whitespace-nowrap focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9">
          <p>
            Total - {" "}
          </p>
            <span> &nbsp;{currentPage * (totalCards < totalCardsPage ? totalCards : totalCardsPage)} de {totalCards }
            {searchValue != '' ? ' em termo: ' +searchValue : ''}

            </span>
        </div>
  )
}
export default TotalFilters