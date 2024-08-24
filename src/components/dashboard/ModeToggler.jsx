import { Button } from '@/components/ui/button';
import { Moon, Sun } from 'lucide-react';
import React, { useState, useEffect } from 'react';

const ModeToggler = props => {
  const [mode, setMode] = useState(() => {
    // Recupera o tema do localStorage ou define o padrão como 'light'
    return localStorage.getItem('theme') || 'dark';
  });

  useEffect(() => {
    const body = document.body;
    if (mode === 'dark') {
      body.classList.add('dark');
    } else {
      body.classList.remove('dark');
    }
  }, [mode]);

  const handleModeToggle = () => {
    const newMode = mode === 'dark' ? 'light' : 'dark';
    setMode(newMode);
    localStorage.setItem('theme', newMode); // Salva a preferência do tema no localStorage
  };

  return (
    <Button
      variant={"default"}
      size={"icon"}
      className='inline-flex items-center justify-center text-sm font-medium transition-colors rounded-md shadow-sm whitespace-nowrap focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-secondary text-secondary-foreground hover:bg-secondary/80 h-9 w-9'
      onClick={handleModeToggle}
    >
      {mode === 'dark' ? <Sun className='w-5 h-5' /> : <Moon className='w-5 h-5' />}
    </Button>
  );
};

export default ModeToggler;
