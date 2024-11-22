import { createSlice } from "@reduxjs/toolkit";

const initialState = { categories: [], category: {} };

export const categoriesSlice = createSlice({
  name: "categories",
  initialState,
  reducers: {
    getCategories: (state, action) => {
      state.categories = action.payload;
    },
    getCategory: (state, action) => {
      state.category = action.payload;
    },
  },
});

export const { getCategories, getCategory } = categoriesSlice.actions;

export default categoriesSlice.reducer;
