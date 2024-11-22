import  { Component } from 'react';
import { Query } from '@apollo/client/react/components';
import { GET_ORDER_BY_ID } from '../../queries.jsx';
import {OrderDetailsContainer} from './styles/OrderDetails.styled.jsx';

class OrderDetails extends Component {
  render() {
    const { orderId } = this.props.match.params;

    return (
    <OrderDetailsContainer>
      <div className="order-details-container">
        <h2>Order Confirmation</h2>
        <Query 
          query={GET_ORDER_BY_ID} 
          variables={{ id: parseInt(orderId, 10) }}
          fetchPolicy="network-only" // Ensure fresh data
        >
          {({ loading, error, data }) => {
            if (loading) return <p>Loading order details...</p>;
            if (error) return <p>Error loading order details: {error.message}</p>;

            const order = data.order;

            return (
              <div>
                <p>Thank you, <strong>{order.name}</strong>, for your purchase!</p>
                <p><strong>Order ID:</strong> {order.id}</p>
                <p><strong>Phone Number:</strong> {order.phone_number}</p>
                <p><strong>Total:</strong> ${order.total.toFixed(2)}</p>
                <p><strong>Order Date:</strong> {new Date(order.created_at).toLocaleString()}</p>
                
                <h3>Order Items:</h3>
                <ul>
                  {order.items.map((item, index) => (
                    <li key={index}>
                      <strong>Product ID:</strong> {item.product_id} | 
                      <strong> Quantity:</strong> {item.quantity} | 
                      <strong> Price:</strong> ${item.price.toFixed(2)}
                    </li>
                  ))}
                </ul>

                <button onClick={() => this.props.history.push('/')}>
                  Back to Home
                </button>
              </div>
            );
          }}
        </Query>
      </div>
      </OrderDetailsContainer>
  
    );
  }
}

export default OrderDetails;
