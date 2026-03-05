FROM node:20-alpine

WORKDIR /app

# Install dependencies
COPY package*.json ./
RUN npm install --production

# Copy semua file
COPY . .

# Buat folder data
RUN mkdir -p dikzshop && echo '[]' > dikzshop/data.json

# Expose port
EXPOSE 3000

# Jalankan server
CMD ["node", "server.js"]
