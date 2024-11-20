import React, { Component } from 'react';
import { ApolloConsumer } from '@apollo/client';
import gql from 'graphql-tag';

const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      name
    }
  }
`;
const GET_ProductAttributeItems = gql`
  query GetProductAttributeItems {
    productsAttributesItems {
      item_id,
      attribute_name,
      display_value,
      value,
      product_id
    }
  }
`;
const GET_ProductAttributes = gql`
  query GetProductAttributes {
    productsAttributes {
      id,
      attribute_name,
      attribute_type,
      product_id
    }
  }
`;
const GET_ProductsGallery = gql`
query GetProductsGallery {
productsGallery {
image_url,
product_id
}
}
`;
const GET_ProductsPrices = gql`
query GetProductsPrices {
productsPrices {
amount
currency_label
currency_symbol
product_id
}
}
`;
const GET_CategoryByName = gql`
query GetCategoryByName($name: String!) {
category(name: $name) {
name
name
      products {
        id
        name
        in_stock
        stock
        gallery{
          product_id
          image_url
        }
        description
        category
        attributes {
          id
          attribute_name
          items {
            item_id
            attribute_name
            display_value
            value
            product_id
          }
          attribute_type
          product_id
        }
        price {
          amount
          currency_label
          currency_symbol
        }
        brand
        category_id
      }
}
}
`;
const GET_ProductById = gql`
query GetProductById($id: Int!) {
product(id: $id) {
        id
        name
        in_stock
        stock
        gallery{
          product_id
          image_url
        }
        description
        category
        attributes {
          id
          attribute_name
          items {
            item_id
            attribute_name
            display_value
            value
            product_id
          }
          attribute_type
          product_id
        }
        price {
          amount
          currency_label
          currency_symbol
        }
        brand
        category_id
}
}
`;

class App extends Component {
  state = {
    categories: [],
    productsAttributesItems: [],
    productsAttributes: [],
    productsGallery: [],
    productsPrices: [],
    categoryProducts: [],
    selectedCategory: null,
    selectedProduct: null,
    loading: false,
    error: null,
  };

  componentDidMount() {
    this.fetchCategories(this.props.client);
    this.fetchProductsAttributesItems(this.props.client);
    this.fetchProductsAttributes(this.props.client);
    this.fetchProductsGallery(this.props.client);
    this.fetchProductsPrices(this.props.client);
  }

  fetchCategories = (client) => {
    this.setState({ loading: true, error: null });
    client
      .query({ query: GET_CATEGORIES })
      .then((result) => {
        this.setState({ categories: result.data.categories, loading: false });
      })
      .catch((error) => this.setState({ error, loading: false }));
  };

  fetchCategoryProducts = (client, categoryName) => {
    this.setState({ loading: true, error: null, selectedCategory: categoryName });
    client
      .query({ query: GET_CategoryByName, variables: { name: categoryName } })
      .then((result) => {
        this.setState({
          categoryProducts: result.data.category.products,
          loading: false,
        });
      })
      .catch((error) => this.setState({ error, loading: false }));
  };

  fetchProductById = (client,id) => {
    this.setState({ loading: true, error: null, selectedProduct: id });
    client
     .query({ query: GET_ProductById, variables: { id: id } })
     .then((result) => {
        this.setState({
          selectedProduct: result.data.product,
          loading: false,
        });
      })
     .catch((error) => this.setState({ error, loading: false }));
  };

  fetchProductsAttributesItems = (client) => {
    this.setState({ loading: true, error: null });
    client
      .query({ query: GET_ProductAttributeItems })
     .then((result) => {
        this.setState({
          productsAttributesItems: result.data.productsAttributesItems,
          loading: false,
        });
      })
     .catch((error) => this.setState({ error, loading: false }));
  };
  fetchProductsAttributes = (client) => {
    this.setState({ loading: true, error: null });
    client
     .query({ query: GET_ProductAttributes })
     .then((result) => {
        this.setState({
          productsAttributes: result.data.productsAttributes,
          loading: false,
        });
      })
     .catch((error) => this.setState({ error, loading: false }));
  };

  fetchProductsGallery = (client) => {
    this.setState({ loading: true, error: null });
    client
    .query({ query: GET_ProductsGallery })
    .then((result) => {
      this.setState
      ({
        productsGallery: result.data.productsGallery,
        loading: false
        });
        })
        .catch((error) => this.setState({ error, loading: false
          }));
  };

  fetchProductsPrices = (client) => {
    this.setState({ loading: true, error: null });
    client
     .query({ query: GET_ProductsPrices })
     .then((result) => {
        this.setState({
          productsPrices: result.data.productsPrices,
          loading: false,
        });
      })
     .catch((error) => this.setState({ error, loading: false }));
  };
  
        
  
  render() {
    const {
      categories,
      productsAttributesItems,
      productsAttributes,
      productsGallery,
      productsPrices,
      categoryProducts,
      selectedCategory,
      selectedProduct,
      loading,
      error,
    } = this.state;

    return (
      <div className="app-container">
        <h1>Categories</h1>
        {loading && <p>Loading...</p>}
        {error && <p>Error: {error.message}</p>}
        <ul>
          {categories.map((category) => (
            <li key={category.name}>
              <button
                onClick={() => this.fetchCategoryProducts(this.props.client, category.name)}
              >
                {category.name}
              </button>
            </li>
          ))}
        </ul>

        {selectedCategory && (
          <>
            <h2>Category: {selectedCategory}</h2>
            <h3>Products</h3>
            {categoryProducts.length === 0 && <p>No products found for this category.</p>}
            <ul>
              {categoryProducts.map((product) => (
                <li key={product.id}>
                  <button onClick={()=> this.fetchProductById(this.props.client, product.id)}>
                    {product.name}
                  </button>
                </li>
              ))}
            </ul>
            {selectedProduct && (
              <>
                <h4>Selected Product</h4>
                <pre>{JSON.stringify(selectedProduct, null, 2)}</pre>
              </>
            )}
          </>
        )}

        <h1>Products Attributes Items</h1>
        {loading && <p>Loading...</p>}
        {error && <p>Error: {error.message}</p>}
        <ul>
          {productsAttributesItems.map((item) => (
            <li key={Math.random()}>
                {item.product_id}-{item.attribute_name} : {item.display_value}
            </li>
          ))}
        </ul>
        <h1>Products Attributes</h1>
        {loading && <p>Loading...</p>}
        {error && <p>Error: {error.message}</p>}
        <ul>
          {productsAttributes.map((attribute) => (
            <li key={Math.random()}>
              {attribute.product_id} - {attribute.attribute_name} : {attribute.attribute_type} :{attribute.id}
            </li>
          ))}
        </ul>

        <h1>Products Gallery</h1>
        {loading && <p>Loading...</p>}
        {error && <p>Error: {error.message}</p>}
        <ul>
          {productsGallery.map((gallery) => (
            <li key={Math.random()}>
              <p>{gallery.product_id}</p>
              <img src={gallery.image_url} alt={gallery.product_id} width="300px" height="400px" />
              </li>
              ))}
        </ul>

        <h1>Products Prices</h1>
        {loading && <p>Loading...</p>}
        {error && <p>Error: {error.message}</p>}
        <ul>
          {productsPrices.map((price) => (
            <li key={Math.random()}>
              {price.product_id} : {price.amount} {price.currency_label} ({price.currency_symbol})
            </li>
              ))}
        </ul>
      </div>
    );
  }
}

const AppWithApollo = () => (
  <ApolloConsumer>
    {(client) => <App client={client} />}
  </ApolloConsumer>
);

export default AppWithApollo;
