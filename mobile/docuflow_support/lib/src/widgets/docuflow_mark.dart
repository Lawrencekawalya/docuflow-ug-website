import 'package:flutter/material.dart';

class DocuFlowMark extends StatelessWidget {
  const DocuFlowMark({super.key, this.size = 48});

  final double size;

  @override
  Widget build(BuildContext context) => Container(
    width: size,
    height: size,
    decoration: BoxDecoration(
      color: const Color(0xFF2563EB),
      borderRadius: BorderRadius.circular(size * .28),
      boxShadow: const [
        BoxShadow(
          color: Color(0x332563EB),
          blurRadius: 16,
          offset: Offset(0, 6),
        ),
      ],
    ),
    child: Icon(
      Icons.file_present_rounded,
      color: Colors.white,
      size: size * .6,
    ),
  );
}

class DocuFlowWordmark extends StatelessWidget {
  const DocuFlowWordmark({super.key});

  @override
  Widget build(BuildContext context) => const Row(
    mainAxisSize: MainAxisSize.min,
    children: [
      DocuFlowMark(size: 42),
      SizedBox(width: 12),
      Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'DocuFlow',
            style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800),
          ),
          Text(
            'UGANDA SUPPORT',
            style: TextStyle(
              color: Color(0xFF2563EB),
              fontSize: 9,
              fontWeight: FontWeight.w800,
              letterSpacing: 1.8,
            ),
          ),
        ],
      ),
    ],
  );
}
