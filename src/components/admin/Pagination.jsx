import React from "react";
import ReactPaginate from "react-paginate";

import './Pagination.scss';

const Pagination = ({pageCount, currentPage, onPageChange  } )=> {
  return (
    <ReactPaginate
      pageCount={pageCount}
      forcePage={currentPage - 1}
      onPageChange={onPageChange}
      containerClassName="pagination"
      pageRangeDisplayed={3}
      marginPagesDisplayed={2}
      activeClassName="active"
      previousLabel="Anterior"
      nextLabel="Próximo"
    />
  );
};

export default Pagination;
