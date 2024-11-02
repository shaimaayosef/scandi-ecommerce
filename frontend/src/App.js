import React, { Component } from "react";
import { gql } from "@apollo/client";
import { ApolloConsumer } from "@apollo/client";

const GET_PRODUCTS = gql`
  query GetProducts($category: String) {
    products(category: $category) {
      id
      name
      inStock
      description
      category
      brand
      gallery
      prices {
        amount
        currency_label
        currency_symbol
      }
    }
  }
`;

const GET_PRODUCT = gql`
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      description
      category
      brand
      gallery
      prices {
        amount
        currency_label
        currency_symbol
      }
    }
  }
`;

const UPDATE_PRODUCT = gql`
  mutation UpdateProduct(
    $id: String!
    $name: String
    $inStock: Boolean
    $description: String
  ) {
    updateProduct(
      id: $id
      name: $name
      inStock: $inStock
      description: $description
    ) {
      id
      name
      inStock
      description
    }
  }
`;

class App extends Component {
  state = {
    products: [],
    selectedProduct: null,
    loading: false,
    error: null,
  };

  handleGetProducts = (client, category = null) => {
    this.setState({ loading: true, error: null });
    console.log("Fetching products for category:", category);

    client
      .query({
        query: GET_PRODUCTS,
        variables: { category },
        fetchPolicy: 'network-only' // This ensures we always get fresh data
      })
      .then((result) => {
        console.log("Products received:", result.data.products);
        this.setState({ 
          products: result.data.products, 
          loading: false 
        });
      })
      .catch((error) => {
        console.error("Error fetching products:", error);
        this.setState({ 
          error: error.message, 
          loading: false 
        });
      });
  };

  handleGetProduct = (client, id) => {
    this.setState({ loading: true, error: null });
    client
      .query({
        query: GET_PRODUCT,
        variables: { id },
      })
      .then((result) => {
        this.setState({ 
          selectedProduct: result.data.product, 
          loading: false 
        });
      })
      .catch((error) => {
        this.setState({ 
          error: error.message, 
          loading: false 
        });
      });
  };

  handleUpdateProduct = (client, productData) => {
    this.setState({ loading: true, error: null });
    client
      .mutate({
        mutation: UPDATE_PRODUCT,
        variables: productData,
      })
      .then((result) => {
        // Update the products list with the modified product
        const updatedProduct = result.data.updateProduct;
        this.setState(prevState => ({
          products: prevState.products.map(product =>
            product.id === updatedProduct.id ? updatedProduct : product
          ),
          loading: false
        }));
      })
      .catch((error) => {
        this.setState({ 
          error: error.message, 
          loading: false 
        });
      });
  };

  renderProductList = (products) => {
    return products.map(product => (
      <div key={product.id} className="product-card">
        <h3>{product.name}</h3>
        <p>Brand: {product.brand}</p>
        <p>Category: {product.category}</p>
        <p>Description: {product.description}</p>
        <p>In Stock: {product.inStock ? "Yes" : "No"}</p>
        {product.prices && product.prices[0] && (
          <p>Price: {product.prices[0].currency_symbol}{product.prices[0].amount}</p>
        )}
        {product.gallery && product.gallery[0] && (
          <img src={product.gallery[0]} alt={product.name} style={{ maxWidth: "200px" }} />
        )}
      </div>
    ));
  };

  render() {
    const { products, selectedProduct, loading, error } = this.state;

    return (
      <ApolloConsumer>
        {(client) => (
          <div className="app-container">
            <h1>Product Catalog</h1>
            
            <div className="controls">
              <button onClick={() => this.handleGetProducts(client)}>
                Load All Products
              </button>
              <button onClick={() => this.handleGetProducts(client, "clothes")}>
                Load Clothes
              </button>
              <button onClick={() => this.handleGetProducts(client, "tech")}>
                Load Electronics
              </button>
            </div>

            {loading && <p>Loading...</p>}
            {error && <p className="error">Error: {error}</p>}
            
            <div className="products-grid">
              {products.length > 0 && this.renderProductList(products)}
            </div>

            {selectedProduct && (
              <div className="product-detail">
                <h2>{selectedProduct.name}</h2>
                <p>{selectedProduct.description}</p>
              </div>
            )}
          </div>
        )}
      </ApolloConsumer>
    );
  }
}

export default App;