from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import os
import asyncpg
import json

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

class UserCreate(BaseModel):
    name: str
    email: str

# Database connection pool
pool = None

@app.on_event("startup")
async def startup():
    global pool
    pool = await asyncpg.create_pool(
        host=os.getenv('DB_HOST', 'database'),
        port=os.getenv('DB_PORT', '5432'),
        database=os.getenv('DB_NAME', 'appdb'),
        user=os.getenv('DB_USER', 'appuser'),
        password=os.getenv('DB_PASSWORD', 'apppass')
    )

@app.get("/")
async def root():
    return {"message": "Python Backend is running"}

@app.get("/api/health")
async def health():
    return {"status": "healthy", "backend": "python"}

@app.get("/api/users")
async def get_users():
    async with pool.acquire() as conn:
        users = await conn.fetch("SELECT * FROM users ORDER BY id")
        return [dict(user) for user in users]

@app.post("/api/users")
async def create_user(user: UserCreate):
    async with pool.acquire() as conn:
        try:
            result = await conn.fetchrow(
                "INSERT INTO users (name, email) VALUES ($1, $2) RETURNING *",
                user.name, user.email
            )
            return dict(result)
        except Exception as e:
            raise HTTPException(status_code=400, detail=str(e))
