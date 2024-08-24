

import React from 'react'

const TotaisFilters = ({currentPage, totalCards, totalCardsPage, searchValue}) => {
  return (
    <div className="totalProduts">
          <p>
            Total - {" "}
          </p>
            <span> &nbsp;{currentPage * (totalCards < totalCardsPage ? totalCards : totalCardsPage)} de {totalCards }
            {searchValue != '' ? ' em termo: ' +searchValue : ''}

            </span>
        </div>
  )
}

export default TotaisFilters