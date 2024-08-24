import React, { useCallback, useEffect, useState } from "react";
import { columns } from "./components/columns";
import { DataTable } from "./components/data-table";
import Alert from "@/components/dashboard/downloads/AlertUser";
import axios from "axios";

const CACHE_KEY = 'templatesCache';
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutos em milissegundos
function isArrayJSON(data) {
  try {
      const parsedData = JSON.parse(data);
      return Array.isArray(parsedData);
  } catch (e) {
      return false;
  }
}

export default function TaskPage() {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const url = window.location.origin + "/wp-admin/admin-ajax.php";

  const fetchTasks = useCallback(async () => {
    const cachedData = localStorage.getItem(CACHE_KEY);
    if (cachedData) {
      const { data, timestamp } = JSON.parse(cachedData);
      if (Date.now() - timestamp < CACHE_DURATION) {
        setTasks(data);
        setLoading(false);
        return;
      }
    }

    const formData = new FormData();
    formData.append("action", "get_templates_files");

    try {
      setLoading(true);
      const response = await axios.post(url, formData);
      const { data } = response;
      
      if (!data ) {
        throw new Error("Invalid response format");
      }

      setTasks(data);
      localStorage.setItem(CACHE_KEY, JSON.stringify({
        data: data,
        timestamp: Date.now()
      }));
    } catch (err) {
      console.error("Error fetching tasks:", err);
      setError(err.response?.data?.message || "Failed to load tasks");
    } finally {
      setLoading(false);
    }
  }, [url]);

  useEffect(() => {
    fetchTasks();
  }, [fetchTasks]);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <div>{error}</div>;
  }

  console.log(tasks.user.status);
  
  return (
    <div className="flex-col flex-1 hidden h-full p-6 space-y-8 md:flex">
      {tasks.user.status == "visitor" && <Alert />}
      {/* <div className="flex items-center justify-between space-y-2 mt-4">
        <div>
          <h2 className="text-2xl font-bold tracking-tight">Templates!</h2>
        </div>
      </div> */}
      <DataTable resp={tasks} columns={columns({ user:tasks.user })} />
    </div>
  );
}