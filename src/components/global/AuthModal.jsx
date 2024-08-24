import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LogOut, User } from 'lucide-react';
import { AppContext } from '@/lib/context'; // 


const CACHE_KEY = 'templatesCache';
const CACHE_KEY_LOGIN = 'LoginCache';
const CACHE_DURATION = 5 * 60 * 1000; 
const AuthModal = () => {
  const { userAuthModal, setUserAuthModal} = useContext(AppContext);
  console.log(userAuthModal);
  
  
  const [isOpen, setIsOpen] = useState(false);
  const [isLogin, setIsLogin] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [username, setUsername] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');
  const [isSuccess, setIsSuccess] = useState(false);
  const [currentUser, setCurrentUser] = useState(null);

  useEffect(() => { 
    checkLoginStatus();
  }, []);

  const checkLoginStatus = async () => {
    const cachedData = localStorage.getItem(CACHE_KEY_LOGIN);
    if (cachedData) {
      const { data, timestamp } = JSON.parse(cachedData);
      if (Date.now() - timestamp < CACHE_DURATION) {
        setIsLoggedIn(true);
        setCurrentUser(data.user);
        return;
      }
    }
    try {
      const response = await axios.post(appLocalizer.url, new URLSearchParams({
        action: 'check_login_status',
      }));
      
      if (response.data.success) {
        setIsLoggedIn(true);
        setCurrentUser(response.data.data.user);
        
        // Armazene os dados no cache
        localStorage.setItem(CACHE_KEY_LOGIN, JSON.stringify({
          data: response.data.data,
          timestamp: Date.now()
        })); 
        localStorage.removeItem(CACHE_KEY);
      } else {
        setIsLoggedIn(false);
        localStorage.removeItem(CACHE_KEY_LOGIN);
      }
    } catch (error) {
      console.error('Error checking login status:', error);
      setIsLoggedIn(false);
      localStorage.removeItem(CACHE_KEY_LOGIN);
    }
  };
  const handleLogout = async () => {
    try {
      const response = await axios.post(appLocalizer.url, new URLSearchParams({
        action: 'custom_ajax_logout',
      }));
      if (response.data.success) {
        setIsLoggedIn(false);
        setCurrentUser(null);
        window.location.reload();
        localStorage.removeItem(CACHE_KEY);
        localStorage.removeItem(CACHE_KEY_LOGIN);
      }
    } catch (error) {
      console.error('Error logging out:', error);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage('');
    setIsSuccess(false);

    const action = isLogin ? 'custom_ajax_login' : 'custom_ajax_register';
    const nonce = isLogin ? appLocalizer.login_nonce : appLocalizer.register_nonce;
    const url = window.location.origin + "/wp-admin/admin-ajax.php";

    try {
      const response = await axios.post(url, new URLSearchParams({
        action,
        username,
        password,
        email: isLogin ? '' : email,
        security: nonce,
      }));

      if (response.data.success) {
        setMessage(response.data.data.message);
        setIsSuccess(true);

        if (isLogin) {
          setTimeout(() => window.location.reload(), 2000);
          localStorage.removeItem(CACHE_KEY);
        } else {
          setTimeout(() => setIsLogin(true), 2000);
        }
      } else {
        setMessage(response.data.data.message);
      }
    } catch (error) {
      setMessage('An error occurred. Please try again.');
    }
  };

  const handleModalOpen = () => {
    setUserAuthModal(true)
  }
  const handleModalClose = () => {
    setUserAuthModal(false)
  }
  return (
    <>
      <Button 
        // onClick={() => setIsOpen(true)}
        onClick={handleModalOpen}
        className="px-4 py-2 font-medium text-white rounded-md bg-primary"
      >
        <User className="mr-2 h-4 w-4 !text-white" />
        {isLoggedIn ? 'Minha Conta' : 'Acessar'}
      </Button>
      {userAuthModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
          <div className="relative w-full max-w-md p-6 bg-white rounded-md shadow-lg dark:bg-background">
            <div className="text-start">
              {isLoggedIn ? (
                <>
                  <h3 className="text-lg font-semibold md:text-base">Bem-vindo, {currentUser?.display_name}</h3>
                  <p className="mt-2">Você está logado como {currentUser?.user_email}</p>
                  <Button
                    onClick={handleLogout}
                    className="mt-4 !w-full !bg-primary !text-white !font-normal !py-2 !rounded-md hover:!bg-primary transition"
                  >
                    <LogOut className="w-4 h-4 mr-2" />
                    Sair
                  </Button>
                </>
              ) : (
                <>
                  <h3 className="text-lg font-semibold md:text-base">
                    {isLogin ? 'Acessar minha conta' : 'Criar conta'}
                  </h3>
                  <form onSubmit={handleSubmit} className="mt-4 space-y-4">
                  <div className="grid !w-full items-center gap-1.5">
              <Label htmlFor="email">Username</Label>
                <Input
                  type="text"
                  placeholder="Username"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  className="!w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                  required
                />
                </div>
                {!isLogin && (
                  <div className="grid !w-full items-center gap-1.5">
              <Label htmlFor="email">Email</Label>
                  <Input
                    type="email"
                    placeholder="Email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="!w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                    required
                  />
                  </div>
                )}
                <div className="grid !w-full items-center gap-1.5">
                <Label htmlFor="email">Password</Label>
                <Input
                  type="password"
                  placeholder="Password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="!w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                  required
                />
                </div>
                <Button
                  type="submit"
                  className="!w-full !bg-primary !text-white !font-normal !py-2 !rounded-md hover:!bg-primary transition"
                >
                  {isLogin ? 'Acessar' : 'Registrar-me'}
                </Button>
                  </form>
                  <p className="mt-4 text-sm text-gray-600">
                    {isLogin ? "Não tem uma conta?" : "Já tem uma conta? "}
                    <Button
                      onClick={() => {
                        setIsLogin(!isLogin);
                        setMessage('');
                      }}
                      className="font-medium bg-transparent shadow-none text-primary hover:bg-transparent hover:text-primary"
                    >
                      {isLogin ? 'Registrar-me' : 'Acessar' }
                    </Button>
                  </p>
                  {message && (
                    <p className={`mt-2 text-sm ${isSuccess ? 'text-green-600' : 'text-red-600'}`}>
                      {message}
                    </p>
                  )}
                </>
              )}
            </div>
            <Button
              onClick={handleModalClose}
              className="absolute bg-transparent shadow-none top-3 right-3 hover:bg-transparent"
            >
              <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </Button>
          </div>
        </div>
      )}
    </>
  );
};
    // <>
    // <Button 
    //  onClick={() => setIsOpen(true)}
    //  className="px-4 py-2 font-medium text-white rounded-md bg-primary"
    // >
    //   <User className="mr-2 h-4 w-4 !text-white" />
    //   Acessar
    // </Button>
    //   {isOpen && (
    //     <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    //       <div className="relative w-full max-w-md p-6 bg-white rounded-md shadow-lg dark:bg-background">
    //         <div className="text-start">
    //           <h3 className="text-lg font-semibold md:text-base">
    //             {isLogin ? 'Acessar minha conta' : 'Criar conta'}
    //           </h3>
    //           <form onSubmit={handleSubmit} className="mt-4 space-y-4">
    //           <div className="grid !w-full items-center gap-1.5">
    //           <Label htmlFor="email">Username</Label>
    //             <Input
    //               type="text"
    //               placeholder="Username"
    //               value={username}
    //               onChange={(e) => setUsername(e.target.value)}
    //               className="!w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
    //               required
    //             />
    //             </div>
    //             {!isLogin && (
    //               <div className="grid !w-full items-center gap-1.5">
    //           <Label htmlFor="email">Email</Label>
    //               <Input
    //                 type="email"
    //                 placeholder="Email"
    //                 value={email}
    //                 onChange={(e) => setEmail(e.target.value)}
    //                 className="!w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
    //                 required
    //               />
    //               </div>
    //             )}
    //             <div className="grid !w-full items-center gap-1.5">
    //             <Label htmlFor="email">Password</Label>
    //             <Input
    //               type="password"
    //               placeholder="Password"
    //               value={password}
    //               onChange={(e) => setPassword(e.target.value)}
    //               className="!w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
    //               required
    //             />
    //             </div>
    //             <Button
    //               type="submit"
    //               className="!w-full !bg-primary !text-white !font-normal !py-2 !rounded-md hover:!bg-primary transition"
    //             >
    //               {isLogin ? 'Acessar' : 'Registrar-me'}
    //             </Button>
    //           </form>
    //           <p className="mt-4 text-sm text-gray-600">
    //             {isLogin ? "Não tem uma conta?" : "Já tem uma conta? "}
    //             <Button
    //               onClick={() => {
    //                 setIsLogin(!isLogin);
    //                 setMessage('');
    //               }}
    //               className="font-medium bg-transparent shadow-none text-primary hover:bg-transparent hover:text-primary"
    //             >
    //               {isLogin ? 'Registrar-me' : 'Acessar' }
    //             </Button>
    //           </p>
    //           {message && (
    //             <p className={`mt-2 text-sm ${isSuccess ? 'text-green-600' : 'text-red-600'}`}>
    //               {message}
    //             </p>
    //           )}
    //         </div>
    //         <Button
    //           onClick={() => setIsOpen(false)}
    //           className="absolute bg-transparent shadow-none top-3 right-3 hover:bg-transparent"
    //         >
    //           <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    //             <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
    //           </svg>
    //         </Button>
    //       </div>
    //     </div>
    //   )}
    // </>


export default AuthModal;