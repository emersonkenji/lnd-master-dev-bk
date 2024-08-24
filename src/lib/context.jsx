import React, { createContext, useState, useEffect, useCallback, useContext } from "react";
import axios from "axios";

const AppContext = createContext();

const AppProvider = ({ children }) => {
  const url = window.location.origin + "/wp-admin/admin-ajax.php";
  const [userAuthModal, setUserAuthModal] =  useState(false);
  const [state, setState] = useState({
    userData: null,
    theme: "dark",
    cards: [],
    loading: false,
    totalPages: "",
    currentPage: 1,
    totalCards: 0,
    perPage: 30,
    searchText: "",
    activeType: "",
    activeFilter: "all",
    activeOrder: "update_date",
    activeOrderBy: "desc",
    category: "",
    activeCategory: "",
    activePlans: "",
    buttonState: "initialButtonState",
    isLoading: true,
    enableButtons: true,
    userPlans: "",
    userPlan: "",
    userStatus: "",
  });

  const updateState = (updates) => {
    setState((prevState) => ({
      ...prevState,
      ...updates,
    }));
  };

  // const updateModal = (updates) => {
  //   setUserAuthModal((prevState) => ({
  //     ...prevState,
  //     ...updates,
  //   }));
  // };


  const getApiItens = useCallback(async () => {
    const formData = new FormData();
    Object.entries({
      action: "get_catalogo_dashboard",
      userData: state.user,
      page: state.currentPage,
      limit: state.perPage,
      filter: state.activeFilter,
      type: state.activeType,
      order: state.activeOrder,
      order_by: state.activeOrderBy,
      query: state.searchText,
      category: state.activeCategory,
      plans: state.activePlans,
    }).forEach(([key, value]) => {
      formData.append(key, value);
    });

    try {
      updateState({ loading: true });
      const response = await axios.post(url, formData);
      const { data } = response;
      

      if (data.user.status !== "logged") {
        updateState({ isLoading: false, enableButtons: true });
      } else {
        updateState({
          enableButtons: false,
          userPlans: data.plans,
        });
      }
      updateState({
        cards: data.result,
        userData: data.user,
        totalPages: data.totalPages,
        totalCards: data.total,
        category: data.category,
        loading: false,
        userPlan: data.plan,
        userStatus: data.user.status
      });
    } catch (error) {
      console.error("Erro ao buscar os dados da API:", error);
      updateState({ loading: false });
    }
  });
  
  useEffect(() => {
    getApiItens();
  }, [
    state.currentPage,
    state.perPage,
    state.activeType,
    state.activeFilter,
    state.activeOrder,
    state.activeOrderBy,
    state.searchText,
    state.activeCategory,
    state.activePlans,
  ]);

  return (
    <AppContext.Provider value={{ state, updateState, userAuthModal, setUserAuthModal }}>
      {children}
    </AppContext.Provider>
  );
};

export { AppContext, AppProvider };
