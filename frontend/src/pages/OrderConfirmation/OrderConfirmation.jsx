import { Component } from 'react';
import { Mutation } from '@apollo/client/react/components';
import { CREATE_ORDER_WITH_USER_INFO } from '../../queries.jsx';
import { connect } from 'react-redux';
import { resetCart, setShowCart } from '../../store/cartSlice.jsx';
import { OrderConfirmationStyles } from './styles/OrderConfirmation.styled.jsx';


class OrderConfirmation extends Component {
  constructor(props) {
    super(props);
    this.state = {
      name: '',
      phone_number: '',
    };
  }

  // Handle input changes for the form fields
  handleChange = (e) => {
    const { name, value } = e.target;
    this.setState({ [name]: value });
  };

  // Handle form submission and execute the createOrder mutation
  handleProceed = (createOrder) => (e) => {
    e.preventDefault();
    const { cartItems, dispatch, history } = this.props;
    const { name, phone_number } = this.state;

    // Validation: Check if the cart is empty
    if (cartItems.length === 0) {
      alert("Your cart is empty.");
      return;
    }

    // Validation: Ensure all required fields are filled
    if (!name.trim() || !phone_number.trim()) {
      alert("Please fill in all required fields.");
      return;
    }

    // Prepare order items for the mutation
    const items = cartItems.map(item => ({
      product_id: item.product.id,
      quantity: item.quantity,
      price: item.product.price.amount,
    }));

    // Calculate the total price of the order
    const total = cartItems.reduce((acc, item) => acc + item.quantity * item.product.price.amount, 0);

    // Execute the createOrder mutation
    createOrder({
      variables: {
        input: {
          name: name.trim(),
          phone_number: phone_number.trim(),
          items: items,
          total: total,
        }
      }
    })
    .then(response => {
      console.log("Order created successfully:", response.data.createOrder);
      alert("Order placed successfully!");
      // Clear the cart and close the cart overlay
      dispatch(resetCart());
      dispatch(setShowCart(false));
      // Navigate to the Order Details page with the new order ID
      history.push(`/order-details/${response.data.createOrder.id}`);
    })
    .catch(err => {
      console.error("Error creating order:", err);
      alert("Failed to place order. Please try again.");
    });
  };

  render() {
    const { name, phone_number } = this.state;

    return (
      <OrderConfirmationStyles>
      <div className="order-confirmation-container">
        <h2>Confirm Your Order</h2>
        <Mutation mutation={CREATE_ORDER_WITH_USER_INFO}>
          {(createOrder, { loading, error }) => (
            <form onSubmit={this.handleProceed(createOrder)} className="order-confirmation-form">
              <div className="form-group">
                <label htmlFor="name">Name<span className="required">*</span></label>
                <input 
                  type="text" 
                  id="name" 
                  name="name"
                  value={name} 
                  onChange={this.handleChange} 
                  required 
                  placeholder="Enter your name"
                />
              </div>
              <div className="form-group">
                <label htmlFor="phone_number">Phone Number<span className="required">*</span></label>
                <input 
                  type="text" 
                  id="phone_number" 
                  name="phone_number"
                  value={phone_number} 
                  onChange={this.handleChange} 
                  required 
                  placeholder="Enter your phone number"
                />
              </div>
              <button type="submit" disabled={loading}>
                {loading ? 'Processing...' : 'Proceed'}
              </button>
              {error && <p className="error-message">Error: {error.message}</p>}
            </form>
          )}
        </Mutation>
      </div>
      </OrderConfirmationStyles>
    );
  }
}

// Map the Redux state to component props
const mapStateToProps = (state) => ({
  cartItems: state.cart.cartItems,
});

// Connect the component to the Redux store and wrap it with withRouter for navigation
export default connect(mapStateToProps)(OrderConfirmation);
