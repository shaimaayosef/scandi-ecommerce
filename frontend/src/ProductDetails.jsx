import { useQuery } from "@apollo/client";
import { GET_ProductById } from "./queries";

const ProductDetails = ({ productId }) => {
  const { loading, error, data } = useQuery(GET_ProductById, {
    variables: { id: productId },
  });

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error.message}</p>;

  console.log(data);

  return (
    <div>
      <h1>Product Details</h1>
      <pre>{JSON.stringify(data, null, 2)}</pre>
    </div>
  );
};

export default ProductDetails;
