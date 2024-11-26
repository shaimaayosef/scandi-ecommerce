import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  cartItems: JSON.parse(localStorage.getItem("cartItems")) || [],
  showCart: false,
  showModal: false,
  activeCategory: 0,
};

export const cartSlice = createSlice({
  name: "cart",
  initialState,
  reducers: {
    setShowCart: (state, action) => {
      state.showCart = action.payload;
    },
    setShowModal: (state, action) => {
      state.showModal = action.payload;
    },
    setActiveCategory: (state, action) => {
      state.activeCategory = action.payload;
    },
    resetCart: (state) => {
      state.cartItems = [];
    },
    addToCart: (state, action) => {
      state.cartItems.push({
        ...action.payload,
        qty: 1,
      });
    },
    updateCart: (state, action) => {
      const id = action.payload;
      state.cartItems.map((item) =>
        item.key === id ? { ...item, qty: item.qty++ } : item
      );
    },
    removeFromCart: (state, action) => {
      const id = action.payload;
      state.cartItems.map((item, i) =>
        i === id ? { ...item, qty: item.qty > 0 ? item.qty-- : item.qty } : item
      );
    },
    deletItem: (state, action) => {
      const id = action.payload;
      state.cartItems.splice(id, 1);
    },
  },
});

export const {
  setShowCart,
  addToCart,
  updateCart,
  removeFromCart,
  deletItem,
  setShowModal,
  resetCart,
  setActiveCategory,
} = cartSlice.actions;

export default cartSlice.reducer;
