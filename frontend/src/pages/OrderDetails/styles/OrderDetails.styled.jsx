import styled from 'styled-components';

export const OrderDetailsContainer = styled.div`
    max-width: 600px;
    margin: 100px auto;
    padding: 20px;

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    p {
        font-size: 1.1em;
        margin-bottom: 10px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }

    button {
        margin-top: 20px;
        padding: 10px;
        background-color: #5ece7b;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;

        &:hover {
            background-color: #5ece7b;
        }
    }
`;