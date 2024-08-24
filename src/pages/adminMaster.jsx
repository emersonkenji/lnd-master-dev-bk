import React, { useEffect, useState } from "react";
import Cards from "../components/admin/Cards";
import Pagination from "../components/admin/Pagination";
import Filters from "../components/admin/Filters";
import ReactLoading from "react-loading";
import axios from "axios";
import FilterButtons from "../components/admin/FilterButtons";
import FilterOrderType from "../components/admin/FilterOrderType";
import FilterOrder from "../components/admin/FilterOrder";
import TotaisFilters from "../components/admin/TotaisFilters";
import { ToastContainer, toast} from 'react-toastify';

import 'react-toastify/dist/ReactToastify.css';

function AdminMaster() {
  const [cards, setCards] = useState([]);
  const [totalCards, setTotalCards] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState("");
  const [totalCardsPage, setTotalCardsPage] = useState(30);
  const [filterSortChange, setFilterSortChange] = useState("desc");
  const [loading, setLoading] = useState(false);
  const [searchValue, setSearchValue] = useState("");
  const [activeFilter, setActiveFilter] = useState("all");
  const [activeOrderFilter, setActiveOrderFilter] = useState("");
  const [activeOrder, setActiveOrder] = useState("update_date");
  // const [action, setAction] = useState("get_catalogo");

  const url = appLocalizer.apiUrl;
  
  const [updating, setUpdating] = useState(false);

  const notify = (status, msg) => {
    if (status === true) {
      toast.success(msg);
   }
   if (status === false) {
      toast.error(msg);
   }
   if (status === 'wait') {
      toast.warn(msg);
   }
  }

  const data =  {
    action: "get_catalogo",
    page: currentPage,
    limit: totalCardsPage,
    order_by: filterSortChange,
    query: searchValue,
    filter: activeFilter,
    type: activeOrderFilter,
    order: activeOrder,
  };


  const getApiPlugins = (urln) => {
    setLoading(true);
    
    const formData = new FormData();
    Object.entries(data).forEach(([key, value]) => {
      formData.append(key, value);
    });
    axios
      .post(url, formData)
      .then((resp) => {
        const data = resp.data;
        setCards(data.processedResults);
        setTotalPages(data.totalPages);
        setTotalCards(data.total);
        setLoading(false);
        // console.log(data);
      })
      .catch((error) => {
        console.log(error);
      });
  };
// console.log(cards);
  useEffect(() => {
    const cardsUrl = `${url}`;
    getApiPlugins(cardsUrl);
  }, [
    currentPage,
    filterSortChange,
    searchValue,
    activeFilter,
    activeOrderFilter,
    activeOrder
  ]);

  const updateCards = (newCards) => {
    // setCards(newCards);
    const cardsUrl = `${url}`;
    // console.log(cardsUrl);
    getApiPlugins(cardsUrl);
  };

  const handleCurrentPage = ({ selected }) => {
    const newPage = selected + 1;
    if (newPage !== currentPage) {
      setCurrentPage(newPage);
    }
  };

  const handleSortChange = (newSortType) => {
    setFilterSortChange(newSortType);
    // setCurrentPage(1);
  };

  const handleSearchSubmit = (event) => {
    event.preventDefault();
    setCurrentPage(1);
    setSearchValue(event.target[0].value);
  };

  const handleCatUpdate = async (event) => {
    event.preventDefault();
    setUpdating(true);
    const data =  {
      action: 'lnd_update_catalog_ajax'
    };
    const formData = new FormData();
    Object.entries(data).forEach(([key, value]) => {
      formData.append(key, value);
    });
    try {
      const resp = await axios.post(url, formData);
      const data = resp.data;
      
      console.log(data);
      
  
      const cardsUrl = `${url}`;
        
      if (data.status === true) {
         toast.success(data.msg);
         getApiPlugins(cardsUrl);
      }
      if (data.status === false) {
         toast.error(data.msg);
      }
      if (data.status === 'wait') {
         toast.warn(data.msg);
      }
      console.log(data);
    } catch (error) {
      console.log(error);
    } finally {
      setUpdating(false);
    }

  }

  const handleFilterChange = (filter) => {
    setActiveFilter(filter);
    setCurrentPage(1);
    console.log(filter);
  };

  const handleFilterOrderChange = (filter) => {
    setActiveOrderFilter(filter);
    setCurrentPage(1);
    console.log(filter);
  };
  const handleOrderChange = (filter) => {
    setActiveOrder(filter);
    setCurrentPage(1);
    console.log(filter);
  };
  const handleCatClear = (filter) => {
    setActiveFilter("all");
    setActiveOrderFilter("");
    setActiveOrder("update_date");
    setCurrentPage(1);
    setSearchValue("");
  };

  return (
    <div className="plataforma">
      <div>
        <ToastContainer
          position="bottom-right"
          autoClose={10000}
          hideProgressBar={false}
          newestOnTop={false}
          closeOnClick
          rtl={false}
          pauseOnFocusLoss
          draggable
          pauseOnHover
          theme="dark"
        />
      </div>
      <div className="plataformaButton">
        <div className="filterTwo">
          <div className="filterCol">
            <FilterButtons
              activeFilter={activeFilter}
              onFilterChange={handleFilterChange}
            />
            <FilterOrderType
              activeFilter={activeOrderFilter}
              onFilterChange={handleFilterOrderChange}
            />
          </div>
          <div className="filterCol">
            <FilterOrder
              activeFilter={activeOrder}
              onFilterChange={handleOrderChange}
            />
          </div>
        </div>
        <Filters
          onSortChange={handleSortChange}
          onSubmit={handleSearchSubmit}
          onCatUpdate={handleCatUpdate}
          onCatClear={handleCatClear}
          updating={updating}
        />
        {loading && (
          <div className="ajax_load">
            <div className="ajax_load_box">
              {/* <div className="ajax_load_box_circle"></div> */}
              <ReactLoading
                type={"bars"}
                color={"#fff"}
                height={"100%"}
                width={"100%"}
              />
              <div className="ajax_load_box_title">Aguarde, carregando!</div>
            </div>
          </div>
        )}
      </div>
      
      <div className="teste">
      {/* <TesteMui /> */}
      </div>

      <div className="plataformaPagination">
        <TotaisFilters
          currentPage={currentPage}
          totalCards={totalCards}
          totalCardsPage={totalCardsPage}
          searchValue={searchValue}
        />
        <Pagination
          pageCount={totalPages}
          currentPage={currentPage}
          onPageChange={handleCurrentPage}
        />
      </div>
      {cards.length === 0 ? (
        <p>Nenhum Plugin ou tema encontrado nesses filtros selecionado.</p>
      ) : (
        <div className="container">
          {cards.map((card) => (
            <Cards key={card.id} card={card} updateCards={updateCards}/>
          ))}
        </div>
      )}

      <div className="plataformaPagination">
        <TotaisFilters
          currentPage={currentPage}
          totalCards={totalCards}
          totalCardsPage={totalCardsPage}
          searchValue={searchValue}
        />
        <Pagination
          pageCount={totalPages}
          currentPage={currentPage}
          onPageChange={handleCurrentPage}
        />
      </div>
    </div>
  );
}

export default AdminMaster;
