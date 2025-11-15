import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { setRedirectHandler } from '../services/api';

const ApiRedirectHandler = ({ children }) => {
  const navigate = useNavigate();

  useEffect(() => {
    // Set the redirect handler to use React Router's navigate function
    setRedirectHandler((path) => {
      navigate(path, { replace: true });
    });

    // Cleanup function to reset redirect handler when component unmounts
    return () => {
      // We could reset the handler, but it's not strictly necessary since
      // it will be reinitialized when the component mounts again
    };
  }, [navigate]);

  return children;
};

export default ApiRedirectHandler;