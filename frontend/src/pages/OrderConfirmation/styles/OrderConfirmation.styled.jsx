import styled from 'styled-components';

export const OrderConfirmationStyles = styled.div`
.order-confirmation-container {
  max-width: 1000px;
  height: 100%;
  margin: 100px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 10px;
}
  .form-group {
    margin-bottom: 15px;
  }

  label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }

  .required {
    color: red;
  }

  input {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
  }

  button {
    width: 100%;
    padding: 10px;
    background-color: #5ece7b;
    color: white;
    font-size: large;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .error-message {
    color: red;
    margin-top: 10px;
  }
`;