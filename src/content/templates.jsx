import React from 'react'
import TaskPage from '@/components/dashboard/tasks/page'
const templates = () => {
  return (
    <div className="flex flex-col min-h-screen">
      <main className="flex items-center justify-center flex-grow text-center ">
        <TaskPage />
      </main>
    </div>
  );
}


export default templates