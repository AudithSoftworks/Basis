// Copyright 2013 Google Inc. All Rights Reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// Helpers common across multiple parts of woff2

#include <algorithm>

#include "./woff2_common.h"

namespace woff2 {


uint32_t ComputeULongSum(const uint8_t* buf, size_t size) {
  uint32_t checksum = 0;
  for (size_t i = 0; i < size; i += 4) {
    // We assume the addition is mod 2^32, which is valid because unsigned
    checksum += (buf[i] << 24) | (buf[i + 1] << 16) |
      (buf[i + 2] << 8) | buf[i + 3];
  }
  return checksum;
}

size_t CollectionHeaderSize(uint32_t header_version, uint32_t num_fonts) {
  size_t size = 0;
  if (header_version == 0x00020000) {
    size += 12;  // ulDsig{Tag,Length,Offset}
  }
  if (header_version == 0x00010000 || header_version == 0x00020000) {
    size += 12   // TTCTag, Version, numFonts
      + 4 * num_fonts;  // OffsetTable[numFonts]
  }
  return size;
}

} // namespace woff2
